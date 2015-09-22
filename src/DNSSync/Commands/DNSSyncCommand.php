<?php

namespace DNSSync\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use DNSSync\Application\DNSSynchronizer;
use DNSSync\Application\Adapters\BestHostingDNSRecordsAdapter;
use DNSSync\Application\FileBuilder;

class DNSSyncCommand extends ConfigCommand{

    protected function configure(){   
        $this->setName("dns-sync:dns")
             ->setDescription("Sync DNS records to tmp directory")
//              ->setDefinition(array(
// 					new InputOption('only-new', 'n', InputOption::VALUE_OPTIONAL, 'Create only new records?', false),
//					new InputOption('stop', 'e', InputOption::VALUE_OPTIONAL, 'stop number of the range of Fibonacci number', $stop)
//                 ))
             ->setHelp("Automatic DNS synchronization");
    }

    protected function execute(InputInterface $input, OutputInterface $output){
//         $start = intval($input->getOption('start'));
	    $dnsSynchronizer = new DNSSynchronizer($this->config);
//     	$dnsSynchronizer->createPTRRecords(new BestHostingPTRAdapter($this->config));
// var_dump($this->config);
		$dnsRecordsAdapter = $this->config["data-adapter.dns-records"];
		$dnsSynchronizer->createDNSRecords(new $dnsRecordsAdapter($this->config)); //TODO načítat z konfigurace
    	
//     	$fileBuilder = new FileBuilder($this->config);
//     	//přesun nových záznamů
//     	$fileBuilder->clearDirectory($this->config['path-pri-backup']);
//     	$fileBuilder->moveDirectory($this->config['path-pri'], $this->config['path-pri-backup']);
//     	$fileBuilder->moveDirectory($this->config['path-pri-tmp'], $this->config['path-pri']);
    	
//     	//přesun seznamu zónových souborů
//     	//         $fileBuilder->saveContent($dnsBuilder->getZoneList(), $this->config['path-zones-tmp']);
//     	$fileBuilder->moveFile($this->config['path-zones'], $this->config['path-zones-backup']);
//     	$fileBuilder->moveFile($this->config['path-zones-tmp'], $this->config['path-zones']);
    	
        $output->writeln('DNS records has been synchronized.');
    }
}
