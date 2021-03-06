<?php 
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use SynKnot\Commands\ReloadCommand;
use SynKnot\Commands\RestartCommand;
use SynKnot\Commands\DNSSyncCommand;
use SynKnot\Commands\PTRSyncCommand;


class ReloadCommandTest extends ApplicationCommandTest{
	public function testReload(){
		$application = $this->getApplication();
		$application->loadCommands();
		// 		var_dump($application);
		$comm = $application->find("synknot:reload");
		
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
		
		$this->assertRegExp("/Reloading service/", $commTester->getDisplay());
	}

	/**
	 * not working
	 */
	public function testNoDatabaseConnection(){
		return;
		$application = $this->getApplication();
		$application->addToConfig(array("password" => "spatne-heslo"));
		$application->loadCommands();
		
		$comm = $application->find("synknot:reload");
		
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
		
		$this->assertEquals("spatne-heslo", $comm->getConfigValue("password"));
		
		$this->setExpectedException('SynKnot\Exception\SynKnotException', "chybka");
		
// 		$this->assertRegExp("/Reloading service/", $commTester->getDisplay());
	}

	/**
	 * not working
	 */
	public function testNoWebserviceConnection(){
 		return;
		
		$application = $this->getApplication();
		$application->loadCommands();
		$comm = $application->find("synknot:reload");
		//$comm->setConfigValue("ptr-password", "spatne-ws-heslo");
		
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
		
// 		$this->setExpectedException('\SynKnot\Exception\SynKnotException', "chybka");
		$this->setExpectedException('SynKnot\Exception\SynKnotException', "chybka");
// 		$this->setExpectedException('SynKnotException', "chybka");
// 		$this->assertRegExp("/Reloading service/", $commTester->getDisplay());
	}
	
	/*
	public function testNoWebserviceConnection2(){
		$app = new Application();
		$app->add(new DNSSyncCommand());
		$app->add(new PTRSyncCommand());
	
		$reload = new ReloadCommand(array("ptr-password" => "spatne-ws-heslo1"));
		$reload->addToConfig(array("ptr-password" => "spatne-ws-heslo2"));
		$app->add($reload);
		$app->add(new RestartCommand());
	
		// 		$comm = $this->getApplication()->find("synknot:reload");
		// 		$comm->addToConfig(array("ptr-user" => "spatne-ws-heslo"));
	
		$comm = $app->find("synknot:reload");
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
	
		// 		$this->setExpectedException('\SynKnot\Exception\SynKnotException', "chybka");
		// 		$this->setExpectedException('SynKnot\Exception\SynKnotException', "chybka");
		// 		$this->setExpectedException('SynKnotException', "chybka");
		$this->assertRegExp("/Reloading service/", $commTester->getDisplay());
	}
	*/
}