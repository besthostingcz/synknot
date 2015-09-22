<?php
namespace DNSSync\Application\Adapters;

use DNSSync\Exception\DNSSyncException;

class TestPTRAdapter extends AbstractDataAdapter{
	public function getData(){
		$data = array();
		$data[] = array(
			'ip' => "8.8.8.8",
			'ptr' => "dns.google.com.",
		);
		$data[] = array(
			'ip' => "8.8.4.4",
			'ptr' => "dns.google.com.",
		);
		$data[] = array(
			'ip' => "13.14.15.16",
			'ptr' => "test.something.cz.",
		);
		$data[] = array(
			'ip' => "13.14.15.17",
			'ptr' => "other.test.com.",
		);
		$data[] = array(
			'ip' => "13.14.15.22",
			'ptr' => "testme.cz.",
		);
		
		return $data;
	}
}