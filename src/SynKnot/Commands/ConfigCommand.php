<?php 
namespace SynKnot\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use SynKnot\Application\FileBuilder;
use SynKnot\Exception\SynKnotException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
// use Symfony\Component\Filesystem\


abstract class ConfigCommand extends Command{//ContainerAwareCommand{
	protected $config = array();
	
	public function __construct(array $config = null){
		parent::__construct();
		
		//jiné parametry
		if(is_array($config)){
			$this->config = $config;
// 			$this->addToConfig($config);
		}
	}
	
	public function getConfigValue($id){
		if(isset($this->config[$id])){
			return $this->config[$id];
		}
		return null;
	}
	
	public function setConfigValue($id, $value){
		$this->config[$id] = $value;
	}
	
// 	protected function execute(InputInterface $input, OutputInterface $output){
// 		// create the lock
// 		$lock = new LockHandle('update:contents');
// 		if (!$lock->lock()) {
// 			$output->writeln('The command is already running in another process.');
	
// 			return 0;
// 		}
	
// 		// ... do some task
	
// 		// (optional) release the lock (otherwise, PHP will do it
// 		// for you automatically)
// 		$lock->release();
// 	}
	
	
// 	private function checkSingleInstance(){
// 		$fb = new FileBuilder($this->config);
// 		$lockDir = $this->config['lockdir'];
		
// 		if(is_dir($lockDir)){
// 			throw new SynKnotException("Running multiple instances is not allowed.");	
// 		}
		
// 		$fb->mkdir($lockDir);

	//;directory for single instance lock
	//lockdir = /var/lock/dns-sync
// 	}
}