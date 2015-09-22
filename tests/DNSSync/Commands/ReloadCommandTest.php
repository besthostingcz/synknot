<?php 
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use DNSSync\Commands\ReloadCommand;
use DNSSync\Commands\RestartCommand;
use DNSSync\Commands\DNSSyncCommand;
use DNSSync\Commands\PTRSyncCommand;


class ReloadCommandTest extends ApplicationCommandTest{
	public function testReload(){
		$application = $this->getApplication();
		$application->loadCommands();
		// 		var_dump($application);
		$comm = $application->find("dns-sync:reload");
		
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
		
		$comm = $application->find("dns-sync:reload");
		
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
		
		$this->assertEquals("spatne-heslo", $comm->getConfigValue("password"));
		
		$this->setExpectedException('DNSSync\Exception\DNSSyncException', "chybka");
		
// 		$this->assertRegExp("/Reloading service/", $commTester->getDisplay());
	}

	/**
	 * not working
	 */
	public function testNoWebserviceConnection(){
 		return;
		
		$application = $this->getApplication();
		$application->loadCommands();
		$comm = $application->find("dns-sync:reload");
		//$comm->setConfigValue("ptr-password", "spatne-ws-heslo");
		
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
		
// 		$this->setExpectedException('\DNSSync\Exception\DNSSyncException', "chybka");
		$this->setExpectedException('DNSSync\Exception\DNSSyncException', "chybka");
// 		$this->setExpectedException('DNSSyncException', "chybka");
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
	
		// 		$comm = $this->getApplication()->find("dns-sync:reload");
		// 		$comm->addToConfig(array("ptr-user" => "spatne-ws-heslo"));
	
		$comm = $app->find("dns-sync:reload");
		$commTester = new CommandTester($comm);
		$commTester->execute(array(
			"command" => $comm->getName(),
		));
	
		// 		$this->setExpectedException('\DNSSync\Exception\DNSSyncException', "chybka");
		// 		$this->setExpectedException('DNSSync\Exception\DNSSyncException', "chybka");
		// 		$this->setExpectedException('DNSSyncException', "chybka");
		$this->assertRegExp("/Reloading service/", $commTester->getDisplay());
	}
	*/
}