<?php 

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use SynKnot\Commands\ConfigCommand;

class RestartCommandText extends ApplicationCommandTest{
	public function testReload(){
		$application = $this->getApplication();
		$application->loadCommands();
		$comm = $application->find("synknot:restart");
		
		$commTester = new CommandTester($comm);
		$commTester->execute(array("command" => $comm->getName()));
		
		$this->assertRegExp("/Restarting service/", $commTester->getDisplay());
	}
}