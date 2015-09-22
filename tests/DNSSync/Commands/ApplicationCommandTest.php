<?php 

use DNSSync\Commands\ConfigCommand;
use DNSSync\Commands\ReloadCommand;
use DNSSync\Commands\RestartCommand;
use DNSSync\Commands\DNSSyncCommand;
use DNSSync\Commands\PTRSyncCommand;
use Symfony\Component\Console\Application;
use DNSSync\Application\DNSSyncApplication;

class ApplicationCommandTest extends \PHPUnit_Framework_TestCase{
	protected function getApplication(){
		$app = new DNSSyncApplication();
// 		$app->add(new DNSSyncCommand());
// 		$app->add(new PTRSyncCommand());
// 		$app->add(new ReloadCommand());
// 		$app->add(new RestartCommand());
		
		return $app;
	}
	
	public function testDumb(){
		$this->assertTrue(true);
	}
}