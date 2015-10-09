<?php

namespace SynKnot\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class TestCommand extends ConfigCommand{

    protected function configure()
    {   
        $start = 0;
        $stop = 100;

        $this->setName("synknot:test")
             ->setDescription("Testing the adapters, if they return right values.");
//              ->setDefinition(array(
//                       new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'Start number of the range of Fibonacci number', $start),
//                       new InputOption('stop', 'e', InputOption::VALUE_OPTIONAL, 'stop number of the range of Fibonacci number', $stop)
//                 ))
//              ->setHelp();
    }

    protected function execute(InputInterface $input, OutputInterface $output){
    	$dnsAdapterClass = $this->getConfigValue("data-adapter.dns-records");
    	$dnsAdapter = new $dnsAdapterClass($this->config);
    	$dnsData = $dnsAdapter->getData();
    	
    	if(count($dnsData) < 1){
    		$output->writeln('Error: DNS data count is smaller then 1');
    	}else{
	    	$output->writeln(sprintf('There are %1$s DNS zones', count($dnsData)));
    	}

    	$zoneFieldsOk = true;
		$fields = array('domainName', 'dnssec', 'name', 'type', 'content', 'ttl', 'priority');
    	foreach ($dnsData as $zoneKey => $dnsZone){
    		foreach ($dnsZone as $recordKey => $dnsRecord){
	    		foreach ($fields as $field){
		    		if(!isset($dnsRecord[$field])){
		    			$zoneFieldsOk = false;
		    			$output->writeln(sprintf('Missing field %1$s in DNS zone %2$s, record %3$s', $field, $zoneKey, $recordKey));
		    		}
	    		}
    		}
    	}
    	
    	if($zoneFieldsOk){
			$output->writeln('Required fields in DNS records are ok');    		
    	}
    	
		// PTR simple test    	
    	$ptrAdapterClass = $this->getConfigValue("data-adapter.ptr-records");
    	$ptrAdapter = new $ptrAdapterClass($this->config);
    	
    	$ptrData = $ptrAdapter->getData();
    	

    	if(count($ptrData) < 1){
    		$output->writeln('Error: PTR data count is smaller then 1');
    	}else{
    		$output->writeln(sprintf('There are %1$s PTR records', count($ptrData)));
    	}
    	 
    	$ptrFieldsOk = true;
    	$fields = array('ip', 'ptr');
		foreach ($ptrData as $ptrKey => $ptr){
			foreach ($fields as $field){
				if(!isset($ptr[$field])){
					$ptrFieldsOk = false;
					$output->writeln(sprintf('Missing field %1$s in PTR record %2$s', $field, $ptrKey));
				}
			}
		}
		
    	if($ptrFieldsOk){
			$output->writeln('Required fields in PTR records are ok');    		
    	}
    }
    
//     private function hasArray
}

//         $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
//         $output->getFormatter()->setStyle('header', $header_style);

//         $start = intval($input->getOption('start'));
//         $stop  = intval($input->getOption('stop'));

//         if ( ($start >= $stop) || ($start < 0) ) {
//            throw new \InvalidArgumentException('Stop number should be greater than start number');
//         }

//         $output->writeln('<header>Fibonacci numbers between '.$start.' - '.$stop.'</header>');
		

//volání jiných příkazů
/*
 $command = $this->getApplication()->find('demo:greet');

 $arguments = array(
 'command' => 'demo:greet',
 'name'    => 'Fabien',
 '--yell'  => true,
 );

 $input = new ArrayInput($arguments);
 $returnCode = $command->run($input, $output);
 */
