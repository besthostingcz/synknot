<?php 
namespace DNSSync\Application;

class PTRRecord{
	private $ip;
	private $ptr;
	
	public function __construct($ip, $ptr){
		$this->setIp($ip);
		$this->setPtr($ptr);
	}
	
	public function build(){
		if(strpos($this->ip, ".") !== false){
// 			var_dump($this->ip);
			$chunks = explode(".", $this->ip);
			$host = $chunks[3];
		}else{
			$binHost = bin2hex(inet_pton($this->ip)); // 2001067c24f400be0000000000000002
			$binHostDot = str_split($binHost);
			$hostFull = implode(".", array_reverse($binHostDot)); // 2.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.e.b.0.0.4.f.4.2.c.7.6.0.1.0.0.2
			$host = substr($hostFull, 0, 39); // 2.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.e.b.0.0
		}
		return sprintf("%s 86400 IN PTR %s", $host, $this->ptr);
	}

	public function getIp() {
		return $this->ip;
	}

	public function setIp($ip) {
		$this->ip = $ip;
		return $this;
	}

	public function getPtr() {
		return $this->ptr;
	}

	public function setPtr($ptr) {
		$this->ptr = $ptr;
		return $this;
	}
	
}