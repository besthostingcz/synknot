<?php

namespace SynKnot\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use SynKnot\Application\DNSSynchronizer;
use SynKnot\Exception\SynKnotException;
use SynKnot\Application\FileBuilder;

class RestartCommand extends ConfigCommand{

    protected function configure(){   
        $this->setName("synknot:restart")
             ->setDescription("Sync DNS and PTR records + restart service")
//              ->setDefinition(array(
//                       new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'Start number of the range of Fibonacci number', $start),
//                       new InputOption('stop', 'e', InputOption::VALUE_OPTIONAL, 'stop number of the range of Fibonacci number', $stop)
//                 ))
             ->setHelp("Sync new DNS and PTR records + restart service");
    }

    protected function execute(InputInterface $input, OutputInterface $output){
//         $start = intval($input->getOption('start'));
//         $stop  = intval($input->getOption('stop'));

    	$dnsCommand = $this->getApplication()->find('synknot:dns');
    	$dnsCommand->run($input, $output);

    	$ptrCommand = $this->getApplication()->find('synknot:ptr');
    	$ptrCommand->run($input, $output);
		
		$fileBuilder = new FileBuilder($this->config);
		if(is_dir($this->config['path-timers'])){
			$fileBuilder->clearDirectory($this->config['path-timers'], array('*.mdb'));
		}
		
		//přesun nových záznamů
		$fileBuilder->clearDirectory($this->config['path-pri-backup']);
		$fileBuilder->moveDirectory($this->config['path-pri'], $this->config['path-pri-backup']);
		$fileBuilder->moveDirectory($this->config['path-pri-tmp'], $this->config['path-pri']);
		
		//přesun seznamu zónových souborů
		$fileBuilder->moveFile($this->config['path-zones'], $this->config['path-zones-backup']);
		$fileBuilder->moveFile($this->config['path-zones-tmp'], $this->config['path-zones']);

		$fileBuilder = new FileBuilder($this->config);
		//přesun nových záznamů
		$fileBuilder->clearDirectory($this->config['path-ptr-backup']);
		$fileBuilder->moveDirectory($this->config['path-ptr'], $this->config['path-ptr-backup']);
		$fileBuilder->moveDirectory($this->config['path-ptr-tmp'], $this->config['path-ptr']);
		
		//přesun seznamu zónových souborů
		$fileBuilder->moveFile($this->config['path-zones-ptr'], $this->config['path-zones-ptr-backup']);
		$fileBuilder->moveFile($this->config['path-zones-ptr-tmp'], $this->config['path-zones-ptr']);

		//restart
    	$dnsSynchronizer = new DNSSynchronizer($this->config);
    	$dnsSynchronizer->restartService();
    	
		$output->writeln('Restarting service.');
    }
}
