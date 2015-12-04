<?php
namespace SynKnot\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SynKnot\Commands\PTRSyncCommand;
use SynKnot\Commands\DNSSyncCommand;
use SynKnot\Commands\ReloadCommand;
use SynKnot\Commands\RestartCommand;
use SynKnot\Exception\SynKnotException;
use Symfony\Component\Console\ConsoleEvents;
use SynKnot\Commands\TestCommand;

class DNSSyncApplication extends Application{
	private $config = array();
	private $logger;
	
	public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN'){
		parent::__construct($name, $version);
		
		$mainDirectory = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
		$configPath = $mainDirectory . 'config.ini';
		$this->config = parse_ini_file($configPath . ".dist");
// var_dump(parse_ini_file($configPath));
		$this->addToConfig(parse_ini_file($configPath));
	}
	
	public function loadCommands(){
		$this->add(new DNSSyncCommand($this->config));
		$this->add(new PTRSyncCommand($this->config));
		$this->add(new ReloadCommand($this->config));
		$this->add(new RestartCommand($this->config));
		$this->add(new TestCommand($this->config));
	}
	
	public function run(InputInterface $input = null, OutputInterface $output = null){
		$fp = fopen($this->config['lockfile'], "w");

		// if you run this script as root - change user/group
		if (file_exists($this->config['lockfile'])) {
			chown($this->config['lockfile'], $this->config['file-user']);
			chgrp($this->config['lockfile'], $this->config['file-group']);
		}
	
		$exitCode = 0;
		
		if (flock($fp, LOCK_EX | LOCK_NB)) {  // acquire an exclusive lock
			ftruncate($fp, 0);

			$exitCode = parent::run($input, $output); 
			
			fflush($fp);            // flush output before releasing the lock
			flock($fp, LOCK_UN);    // release the lock
		} else {
			//throw new DNSSyncException("Running multiple instances is not allowed."); - nezachytí applikace error
			//$output->writeln() - null v této chvíli
			$message = "Running multiple instances is not allowed.";
			echo $message . PHP_EOL;
			mail($this->config['admin-email'], $message, $message);
			
			$exitCode = 500;
		}

		fclose($fp);
		
		return $exitCode; 
	}
	
	public function addToConfig(array $config){
		$this->config = array_merge($this->config, $config);
	}
}
