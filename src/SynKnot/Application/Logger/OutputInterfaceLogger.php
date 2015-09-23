<?php 
namespace SynKnot\Application\Logger;

use Symfony\Component\Console\Output\OutputInterface;
class OutputInterfaceLogger implements ILogger{
	private $outputInterface;
	
	public function __construct(OutputInterface $output){
		$this->outputInterface = $output;
	}
	
	public function log($message, $priority = "INFO") {
		//$this->outputInterface->writeln(sprintf('%1$s %2$s %3$s', $priority, date("Y-m-d H:i:s"), $message));
		$this->outputInterface->writeln(sprintf('%1$s %2$s %3$s', $priority, date("[H:i:s]"), $message));
	}
}