<?php 
namespace SynKnot\Application;

use SynKnot\Exception\MissingSOAException;
use SynKnot\Exception\SynKnotException;
use \Exception;
use SynKnot\Application\Adapters\IAdapter;
use SynKnot\Application\Logger\ILogger;
use SynKnot\Application\Logger\DummyLogger;

class DNSSynchronizer{
	private $config;
	private $logger;
	
	public function __construct(array $config){
		$this->config = $config;		
		$this->logger = new DummyLogger();
	}
	
	public function createDNSRecords(IAdapter $recordsAdapter){
		$fileBuilder = new FileBuilder($this->config);
		$data = $recordsAdapter->getData();
		$dnsBuilder = new DNSBuilder($this->config);
		
		foreach ($data as $domainName => $rows){
			$soaExits = false;
			
			foreach ($rows as $row){
				$dnsBuilder->addRecord($domainName, $row['type'], $row['name'], $row['content'], $row['ttl'], $row['priority']);
				if($row['type'] == "SOA"){
					$soaExits = true;
				}
			}
			
			try{
				$path = sprintf('%1$s%2$s.zone', $this->config['path-pri-tmp'], $domainName);
				
				switch($this->config['server-status']){
					case "master":
						//obsah zónového souboru
						$content = $dnsBuilder->build($this->config['path-pri']);
						$fileBuilder->saveContent($content, $path);
						//záznam o zónovém souboru do seznamu
						$dnsBuilder->createInfo($this->config['path-pri'], $row['dnssec']);
						
						break;
					
					case "slave":
						//záznam o zónovém souboru do seznamu
						if($soaExits){
							$dnsBuilder->createInfo($this->config['path-pri'], $row['dnssec']);
						}
						break;
				}
			}catch (MissingSOAException $e){
// 				echo $e->getMessage();
				//nikoho nezajímá
			}catch (Exception $e){
				echo $e->getMessage();
			}
			$dnsBuilder->clear();
		}
		
		//přesun nových záznamů
// 		$fileBuilder->clearDirectory($this->config['path-pri-backup']);
// 		$fileBuilder->moveDirectory($this->config['path-pri'], $this->config['path-pri-backup']);
// 		$fileBuilder->moveDirectory($this->config['path-pri-tmp'], $this->config['path-pri']);
		
		//přesun seznamu zónových souborů
		$fileBuilder->saveContent($dnsBuilder->getZoneList(), $this->config['path-zones-tmp']);
// 		$fileBuilder->moveFile($this->config['path-zones'], $this->config['path-zones-backup']);
// 		$fileBuilder->moveFile($this->config['path-zones-tmp'], $this->config['path-zones']);
	}
			
	public function createPTRRecords(IAdapter $dataAdapter){
		$data = $dataAdapter->getData();
		$ptrBuilder = new PTRBuilder($this->config); //temp
		
		
		
		// rozdělení do ptr groups
// 		$ptrBuilders = array();
// 		foreach ($data as $row){
// 			$ptrGroup = $ptrBuilder->getPTRGroup($row["ip"]);
// 			if(!isset($ptrBuilders[$ptrGroup])){
// 				$ptrBuilder = new PTRBuilder($this->config);
// 				$ptrBuilders[$ptrGroup] = $ptrBuilder;
// 			}else{
// 				$ptrBuilder = $ptrBuilders[$ptrGroup];
// 			}
			
// 			if(!empty($row['ptr'])){
// 				$ptrBuilder->addRecord($row["ip"], $row["ptr"]);
// 			}
// 		}
		foreach ($data as $row){
			$ptrBuilder->addRecord($row["ip"], $row["ptr"]);
		}
		
		$fileBuilder = new FileBuilder($this->config);
		
// 		$ptrGroupList = array();
		// vytváření PTR souborů pro každou group
		$this->getLogger()->log("Creating PTR zones");
		foreach ($ptrBuilder->getRecords() as $key => $ptrGroup){
			//uložit do souboru
			
// 			$path = sprintf('%1$s%2$s.zone', $this->config['path-pri-tmp'], $domainName);
			
			switch($this->config['server-status']){
				case "master":
					//obsah zónového souboru
					$path = $this->config['path-ptr-tmp'] . $key . ".zone";
					$pathOld = $this->config['path-ptr'] . $key . ".zone";
					
					$serial = date("Ymd") . "01";
					$ptrZone = $ptrBuilder->build($key, $serial);
					
					//starý zónový soubor
					if(file_exists($pathOld)){
						$this->getLogger()->log("Found old PTR zone " . $pathOld);
						$ptrZoneOld = file_get_contents($pathOld);
						
						try{
							//sériové číslo v původním souboru
							$oldSerial = $this->getSerial($ptrZoneOld);
							$ptrZoneCheck = $this->switchSerial($ptrZoneOld, $serial);
							
							//stará zóna je jiná, než tahle, tak se navýší serial
							if($ptrZoneCheck != $ptrZone){
								$maxSerial = max($serial, intval($oldSerial + 1));
								$ptrZone = $this->switchSerial($ptrZone, $maxSerial);
								$this->getLogger()->log("Different PTR zone > new serial " . $maxSerial);
							}else{
								//zóny jsou stejné
								//if($serial < $oldSerial){
									//kdyby náhodou byl serial menší, tak musí zůstat ten již upravovaný
									$ptrZone = $this->switchSerial($ptrZone, $oldSerial);
									$this->getLogger()->log("Same PTR zone > old serial " . $oldSerial);
								//}else{
									//$this->getLogger()->log("PTR serial should be the same");
								//}
							}
							
							//TODO tests: 
							/*
							 * - jestli se vytvoří zónový soubor na fresh instalaci
							 * - jestli se přepíše a zvětší serial u již hotového zónového souboru, když jsou data jiná
							 * - jestli se nepřepíše serial, když jsou data stejná, ale den větší
							 * - když jsou soubory rozlišné, tak se má vygenerovat nový serial, který je minimálně dnes
							 */
										
						}catch(Exception $e){
							$this->getLogger()->log($e->getMessage());
							throw $e;
						}
					}
					
					$fileBuilder->saveContent($ptrZone, $path);
					$this->getLogger()->log("Saving PTR zone " . $path);
					
					break;
						
				case "slave":
					$ptrZone = $ptrBuilder->build($key); //tím se dostane do zonelist
					//záznam o zónovém souboru do seznamu
					break;
			}
			
			
// 			$ptrGroupList[] = sprintf("%s{\n\tfile \"%s\";\n}", $key, $this->config['path-ptr'] . $key . ".zone");
		}
		
		//vytvoření seznamu všech PTR groups
// 		$ptrBuilder->saveContent($this->config['path-zones-ptr-tmp'], implode(PHP_EOL . PHP_EOL, $ptrGroupList));
		
		//přesun nových záznamů
// 		$fileBuilder->clearDirectory($this->config['path-ptr-backup']);
// 		$fileBuilder->moveDirectory($this->config['path-ptr'], $this->config['path-ptr-backup']);
// 		$fileBuilder->moveDirectory($this->config['path-ptr-tmp'], $this->config['path-ptr']);
		
		//přesun seznamu zónových souborů
		$fileBuilder->saveContent($ptrBuilder->getZoneList(), $this->config['path-zones-ptr-tmp']);
// 		$fileBuilder->moveFile($this->config['path-zones-ptr'], $this->config['path-zones-ptr-backup']);
// 		$fileBuilder->moveFile($this->config['path-zones-ptr-tmp'], $this->config['path-zones-ptr']);
	}
	
	private function switchSerial($zone, $serial){
		$lines = explode(PHP_EOL, $zone);
		if(isset($lines[1])){
			//rozparsovat řádek se SOA podle mezer
			$serialParts = explode(" ", $lines[1]);
			if(isset($serialParts[5])){
				// konkrétní serial: 2015070801
				$serialParts[5] = $serial;
				$lines[1] = implode(" ", $serialParts);
				return implode(PHP_EOL, $lines);
			}
		}
		throw new Exception("There is no serial to switch");
	}

	private function getSerial($zone){
		$lines = explode(PHP_EOL, $zone);
		if(isset($lines[1])){
			//rozparsovat řádek se SOA podle mezer
			$serialParts = explode(" ", $lines[1]);
			if(isset($serialParts[5])){
				// konkrétní serial: 2015070801
				return $serialParts[5];
			}
		}
		
		throw new Exception("There is no serial to get");
	}
	
	public function reloadService(){
		if(!isset($this->config['reload-commands'])){
			throw new SynKnotException("reload-commands are not deffined in config"); 
		}
		
		$commands = $this->config['reload-commands'];
		foreach ($commands as $c){
// 			var_dump($c);
// 			exec('/etc/init.d/knot restart');
			echo exec($c) . PHP_EOL;
		}
	}
	
	public function restartService(){
		if(!isset($this->config['restart-commands'])){
			throw new SynKnotException("restart-commands are not deffined in config"); 
		}
		
		$commands = $this->config['restart-commands'];
		foreach ($commands as $c){
// 			var_dump($c);
// 			exec('/etc/init.d/knot restart');
			echo exec($c) . PHP_EOL;
		}
	}

	public function getLogger() {
		return $this->logger;
	}

	public function setLogger(ILogger $logger) {
		$this->logger = $logger;
		return $this;
	}
}