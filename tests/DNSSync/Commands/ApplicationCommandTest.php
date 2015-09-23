<?php 

use SynKnot\Commands\ConfigCommand;
use SynKnot\Commands\ReloadCommand;
use SynKnot\Commands\RestartCommand;
use SynKnot\Commands\DNSSyncCommand;
use SynKnot\Commands\PTRSyncCommand;
use Symfony\Component\Console\Application;
use SynKnot\Application\DNSSyncApplication;

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