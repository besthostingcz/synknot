<?php 
namespace DNSSync\Application\Logger;

class DummyLogger implements ILogger{
	public function log($message) {
		//very dummy
	}
}