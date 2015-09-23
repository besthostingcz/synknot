<?php
namespace SynKnot\Application;

class DNSRecord{
	private $domainName;
	private $type;
	private $name;
	private $content;
	private $ttl;
	private $priority;
	
	public function __construct($domainName, $type, $name, $content, $ttl, $priority){
		$this->setDomainName($domainName);
		$this->setType($type);
		$this->setName($name);
		$this->setContent($content);
		$this->setTTL($ttl);
		$this->setPriority($priority);
	}
	
	public function build(){
		$content = "";
		switch($this->getType()){
			case "SOA":
				//protože je místo zavináče jinak název domény
				$content = sprintf("@ %22s     %s\n", 
					$this->getType(),
					$this->getContent());
				break;
			
			case "NS":
				$content = sprintf("%-20s %-7s %s\n", 
					$this->getName(),
					$this->getType(),
					$this->getContent());
					//str_replace($this->getDomainName() . ".", "", $this->getContent()));
				break;
			
// 			case "CNAME":
// 				$content = sprintf("%1s\t\t\t%2s\t%3s\n",
// 					$this->getName(),
// 					$this->getType(), 
// 					$this->getContent());
// 				break;			
			
			case "MX": //unique
				$content = sprintf("%-20s %s %4s %s\n", 
					$this->getName(),
					$this->getType(), 
					$this->getPriority(), 
					$this->getContent());
				break;
				
// 			case "A":
// 				$content = sprintf("%1s\t\t\t%2s\t%3s\n", 
// 					$this->getName(), 
// 					$this->getType(), 
// 					$this->getContent());
// 				break;
			
// 			case "AAAA":
// 				$content = sprintf('%1$s%2$s%3$s', $this->getName(), $this->getType(), $this->getContent()) . PHP_EOL;
// 				break;
			
			case "TXT": //unique
				$content = sprintf("%-20s %-7s \"%s\"\n", 
					$this->getName(), 
					$this->getType(),
					$this->getContent());
				break;
				
			case "CNAME":
			case "AAAA":
			case "SRV":
			case "A":
			default:
				$content = sprintf("%-20s %-7s %s\n", 
					$this->getName(), 
					$this->getType(), 
					$this->getContent());
		}
		return $content;
	}

	public function getDomainName() {
		return $this->domainName;
	}

	public function setDomainName($domainName) {
		$this->domainName = $domainName;
		return $this;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function getTTL() {
		return $this->ttl;
	}

	public function setTTL($ttl) {
		$this->ttl = $ttl;
		return $this;
	}

	public function getPriority() {
		return $this->priority;
	}

	public function setPriority($priority) {
		$this->priority = $priority;
		return $this;
	}
}