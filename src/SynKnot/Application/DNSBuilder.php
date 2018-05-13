<?php
namespace SynKnot\Application;

use SynKnot\Exception\MissingSOAException;

class DNSBuilder{
	private $records = array();
	private $recordsNoTTL = array();
	private $domainName;
	private $zoneList = "";
	private $config = array();
	
	public function __construct(array $config){
		$this->config = $config;
	}
	
	public function addRecord($domainName, $type, $name, $content, $ttl, $priority){
		$this->domainName = $domainName;
		
		if(!isset($this->records[$ttl])){
			$this->records[$ttl] = array();
		}
		
		switch ($type){
			case "SOA":
			case "TXT":
				$this->recordsNoTTL[] = new DNSRecord($domainName, $type, $name, $content, $ttl, $priority);
				break;
			default:
				$this->records[$ttl][] = new DNSRecord($domainName, $type, $name, $content, $ttl, $priority);
				break;
		}
	}
	
	public function build($priPath){
		$soaExists = false;
		$content = "";
		$content .= sprintf('$ORIGIN %1$s.', $this->domainName) . PHP_EOL;
		
		//SOA a TXT záznamy
		foreach ($this->recordsNoTTL as $record){
			$content .= $record->build();
			if($record->getType() == "SOA"){
				$soaExists = true;
			}
		}
		
		foreach ($this->records as $ttl => $records){
			$content .= PHP_EOL . sprintf('$TTL %1$s', $ttl) . PHP_EOL; //kdyby TXT neměly žádný TTL, tak to bude haprovat
			//ostatní záznamy
			foreach ($records as $record){
				$content .= $record->build();
				// 				$content .= $this->buildGroup($name, $records);
			}
		}
		
		if(!$soaExists){
			throw new MissingSOAException("There is no SOA record for domain " . $this->getDomainName());
		}
		
		//TODO dnssec dnssec-enable on;
		// 		$this->zoneList .= sprintf('%1$s {file "%2$s%1$s.zone";}',
		// 			$this->getDomainName(),
		// 			$priPath . DIRECTORY_SEPARATOR) . PHP_EOL; // . PHP_EOL;
		return $content;
	}
	
	/**
	 * přidá informaci do zónového seznamu
	 * @param string $priPath
	 */
	public function createInfo($priPath, $dnssec = false){
		// 		$dnssecValue = $dnssec == "1" ? "on" : "off";
		// 		$dnssecText = sprintf('dnssec-enable %1$s;', $dnssecValue);
		
		switch($this->config["server-status"]){
			case "slave":
// 				$serverStatus = "xfr-in " . $this->config["server-master"] . ";\n\tnotify-in " . $this->config["server-master"] . ";";
// 				$this->zoneList .= sprintf("%s {\n\tfile \"%s%s.zone\";\n\t%s\n\t%s\n}\n",
// 						$this->getDomainName(),
// 						$priPath,
// 						$this->getDomainName(),
// 						$serverStatus,
// 						$dnssecText
// 						) . PHP_EOL;
				$this->zoneList .= sprintf("zone:\n  - domain: %s\n",
//				$this->zoneList .= sprintf("zone:\n  - domain: %s\n    storage: %s\n    file: %s.zone\n",
//						$this->getDomainName(),
//						$priPath,
						$this->getDomainName()
						) . PHP_EOL;
						break;
						
			case "master":
				// 				$serverStatus = "xfr-out " . implode(",", $this->config["server-slaves-ip"]) . ";\n\tnotify-out " . implode(",", $this->config["server-slaves"]) . ";";
				// 				$this->zoneList .= sprintf("%s {\n\tfile \"%s%s.zone\";\n\t%s\n\t%s\n}\n",
				// 					$this->getDomainName(),
				// 					$priPath,
				// 					$this->getDomainName(),
				// 					$serverStatus,
				// 					$dnssecText
				// 				) . PHP_EOL;
				
				//$serverStatus = "xfr-out " . implode(",", $this->config["server-slaves-ip"]) . ";\n\tnotify-out " . implode(",", $this->config["server-slaves"]) . ";";
				// 				$slaveList = '';
				// 				foreach ($this->config["server-slaves"] as $slave){
				// 					$slaveList .= sprintf("    notify: %s\n", $slave);
				// 				}
				$slaveList = implode(', ', $this->config["server-slaves"]);
				$dnssecInfo = '';
				if($dnssec == true){
					$dnssecInfo = "    template: signed\n";
				}
				
				$this->zoneList .= sprintf("zone:\n  - domain: %s\n%s",
						$this->getDomainName(),
						$dnssecInfo
						) . PHP_EOL;
// 				$this->zoneList .= sprintf("zone:\n  - domain: %s\n    storage: %s\n    file: %s.zone\n    notify: [%s]\n%s",
// 						$this->getDomainName(),
// 						$priPath,
// 						$this->getDomainName(),
// 						$slaveList,
// 						$dnssecInfo
// 						) . PHP_EOL;
								
						break;
						
			case "default":
				$this->zoneList .= "server-status is incorrect";
		}
	}
	
	// 	public function buildGroup($name, array $records){
	// 		$content = "";
	// 		foreach ($records as $record){
	// 			$record->setName("\t");
	// 			$content .= $record->build();
	// 		}
	// 		return $content;
	// 	}
	
	
	public function clear(){
		$this->setDomainName("");
		$this->setRecords(array());
		$this->setRecordsNoTTL(array());
	}
	
	public function getRecords() {
		return $this->records;
	}
	
	public function setRecords($records) {
		$this->records = $records;
		return $this;
	}
	
	public function getDomainName() {
		return $this->domainName;
	}
	
	public function setDomainName($domainName) {
		$this->domainName = $domainName;
		return $this;
	}
	
	public function getRecordsNoTTL() {
		return $this->recordsNoTTL;
	}
	
	public function setRecordsNoTTL($recordsNoTTL) {
		$this->recordsNoTTL = $recordsNoTTL;
		return $this;
	}
	
	public function setZoneList($zoneList) {
		$this->zoneList = $zoneList;
		return $this;
	}
	
	public function getZoneList(){
		return $this->zoneList;
	}
}
