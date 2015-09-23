#!/usr/bin/env php
<?php

// set to run indefinitely if needed
set_time_limit(0);

date_default_timezone_set('Europe/Prague'); 

// include the composer autoloader
require_once __DIR__ . '/vendor/autoload.php'; 

// import the Symfony Console Application 
use Symfony\Component\Console\Application; 
use Symfony\Component\Console\Command\Command;
// use SynKnot\Commands\TestCommand;
// use SynKnot\Commands\PTRSyncCommand;
// use SynKnot\Commands\DNSSyncCommand;
// use SynKnot\Commands\ReloadCommand;
// use SynKnot\Commands\RestartCommand;
use SynKnot\Application\DNSSyncApplication;


// $fp = fopen("/var/lock/dns-sync.lock", "w");

// if (flock($fp, LOCK_EX | LOCK_NB)) {  // acquire an exclusive lock
// 	ftruncate($fp, 0);
	
	$app = new DNSSyncApplication();
	$app->loadCommands();
// 	$app->add(new DNSSyncCommand());
// 	$app->add(new PTRSyncCommand());
// 	$app->add(new ReloadCommand());
// 	$app->add(new RestartCommand());
	$app->run();

// 	fflush($fp);            // flush output before releasing the lock
// 	flock($fp, LOCK_UN);    // release the lock
// } else {
// 	echo "Running multiple instances is not allowed." . PHP_EOL;
// }

// fclose($fp);
