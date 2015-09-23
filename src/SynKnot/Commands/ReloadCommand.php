<?php

namespace SynKnot\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use SynKnot\Application\DNSSynchronizer;
use SynKnot\Application\FileBuilder;
use SynKnot\Exception\SynKnotException;
use \DateTime;
use \DateInterval;

class ReloadCommand extends ConfigCommand{

    protected function configure(){   
        $this->setName("synknot:reload")
             ->setDescription("Sync new DNS and PTR records + reload service")
//              ->setDefinition(array(
//                       new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'Start number of the range of Fibonacci number', $start),
//                       new InputOption('stop', 'e', InputOption::VALUE_OPTIONAL, 'stop number of the range of Fibonacci number', $stop)
//                 ))
             ->setHelp("Sync new DNS and PTR records + restart service");
    }

    protected function execute(InputInterface $input, OutputInterface $output){
//         $start = intval($input->getOption('start'));
//         $stop  = intval($input->getOption('stop'));

//     	sleep(10);
//     	throw new SynKnotException("chybka");
//     	var_dump("executing reload");
    	
    	//nasype DNS (pri) zóny do temp adresáře
    	$dnsCommand = $this->getApplication()->find('synknot:dns');
    	$dnsCommand->run($input, $output);

    	//nasype PTR zóny do tmp adresáře
    	$ptrCommand = $this->getApplication()->find('synknot:ptr');
    	$ptrCommand->run($input, $output);
    	
        $fileBuilder = new FileBuilder($this->config);
        //přesun nových záznamů
        //temp do používaného adresáře
        $fileBuilder->moveDirectory($this->config['path-pri-tmp'], $this->config['path-pri']);
        //přesun seznamu zónových souborů
        $fileBuilder->moveFile($this->config['path-zones-tmp'], $this->config['path-zones']);

        //pro PTR to platí taky
        $fileBuilder->moveDirectory($this->config['path-ptr-tmp'], $this->config['path-ptr']);
        $fileBuilder->moveFile($this->config['path-zones-ptr-tmp'], $this->config['path-zones-ptr']);
        
        //reload
        $dnsSynchronizer = new DNSSynchronizer($this->config);
    	$dnsSynchronizer->reloadService();
    	
        $output->writeln('Reloading service.');
        
        //TODO 
        //$this->getContainer()->get('logger')->info("Reload complete");
//         $this->logger->info()
    }
}
