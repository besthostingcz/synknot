<?php 
namespace SynKnot\Application;

class PTRBuilder{
	private $records = array();
	private $config;
	private $zoneList = "zone: \n";
	
	public function __construct(array $config){
		$this->config = $config;
	}	
	
	public function addRecord($ip, $ptr){
		$ptrGroup = $this->getPTRGroup($ip);
	
		if(!isset($this->records[$ptrGroup])){
			$this->records[$ptrGroup] = array();
		}

		if(!empty($ptr)){
			$this->records[$ptrGroup][] = new PTRRecord($ip, $ptr);
		}
	}
		
	public function build($ptrGroup, $serial = null){
		if(is_null($serial)){
			$serial = date("Ymd") . "01";
		}
		$ptrTtl = $this->config["ptr-ttl"];
		
		$content = "";
		$content .= sprintf('$TTL %1$s', $ptrTtl) . PHP_EOL;
		$content .= sprintf('@ IN SOA %1$s %2$s %3$s %4$s %5$s %6$s %7$s',
							$this->config['ptr-soa-name-server'],
							str_replace("@", ".", $this->config['ptr-soa-admin-email']),
							$serial,
							$this->config['ptr-soa-limit-1'],
							$this->config['ptr-soa-limit-2'],
							$this->config['ptr-soa-limit-3'],
							$this->config['ptr-soa-limit-4']) . PHP_EOL;
		//$content .= "@ IN SOA ns1.best-hosting.cz admin.best-net.cz. " .  $serial  . " 10800 3600 1209600 86400\n";

		foreach ($this->config["ptr-nameserver-list"] as $nameServer){
			$content .= sprintf('@ %1$s IN NS %2$s.', $ptrTtl, $nameServer) . PHP_EOL;
		}
		
		foreach ($this->records[$ptrGroup] as $record){
			$content .= $record->build() . PHP_EOL;
		}
		
		$dnsSec = "dnssec-enable off;";	
		
// 		switch($this->config["server-status"]){
// 			case "slave":
// 				$serverStatus = "xfr-in " . $this->config["server-master"] . ";\n\tnotify-in " . $this->config["server-master"] . ";";
// 				break;
// 			case "master":
// 				$serverStatus = "xfr-out " . implode(",", $this->config["server-slaves-ip"]) . ";\n\tnotify-out " . implode(",", $this->config["server-slaves"]) . ";";
// 				$serverStatus = "xfr-out " . implode(",", $this->config["server-slaves-ip"]) . ";\n\tnotify-out " . implode(",", $this->config["server-slaves"]) . ";";
// 				break;
// 		}
		
// 		$this->zoneList .= sprintf("%s{\n\tfile \"%s.zone\";\n\t%s\n\t%s\n}", 
// 							$ptrGroup, $this->config['path-ptr'] . $ptrGroup, 
// 							$dnsSec, $serverStatus) . PHP_EOL . PHP_EOL;
		$this->zoneList .= sprintf("   - domain: %s \n     template: ptr", 
							$ptrGroup) . PHP_EOL . PHP_EOL;
		return $content;
	}

	public function getZoneList(){
		return $this->zoneList;
	}
	
	private function getPTRGroup($ip){
		//ipv4 adresy
		if(strpos($ip, ".") !== false){
			$reverseIp = $this->reverseIP($ip);
			$group = substr($reverseIp, strpos($reverseIp, ".") + 1);
			return $group . ".in-addr.arpa";
		}
	
		//ipv6 adresy
		// 		$group = "4.f.4.2.c.7.6.0.1.0.0.2.ip6.arpa.zone";
	
		$ipDotLess = str_replace(":", "", $this->fullIPv6($ip)); //2001067c24f400be0000000000000002
		$group = substr($ipDotLess, 0, 12); //2001067c24f400
		$groupDot = implode(".", str_split($group)); //2.0.0.1.0.6.7.c.2.4.f.4
		$reverseIp = $this->reverseIP($groupDot); //4.f.4.2.c.7.6.0.1.0.0.2
	
		return $reverseIp . ".ip6.arpa";
	}
	

	private function reverseIP($ip){
		return implode(".", array_reverse(explode(".", $ip)));
	}

	private function fullIPv6($ip){
		$out = "";
		$chunks = str_split(bin2hex(inet_pton($ip)));
		$i = 0;
		foreach ($chunks as $ch){
			$out .= $ch;
			if($i++ == 3){
				$out .= ":";
				$i = 0;
			}
		}
		return trim($out, ":");
	}

	public function getRecords() {
		return $this->records;
	}

	public function setRecords($records) {
		$this->records = $records;
		return $this;
	}
	
	
}