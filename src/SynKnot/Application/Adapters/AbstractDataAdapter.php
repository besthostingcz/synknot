<?php

namespace SynKnot\Application\Adapters;

abstract class AbstractDataAdapter implements IAdapter{
	protected $config = array();
	
	public function __construct(array $config){
		$this->config = $config;
	}
}

