<?php 
namespace SynKnot\Application\Adapters;

use SynKnot\Exception\SynKnotException;

class TestDNSRecordsAdapter extends AbstractDataAdapter{
	public function getData() {
		$data = array();
		$data[] = array(
			'domainName' => "testdomain",
			'tld' => "cz",
			'name' => "testdomain.cz",
			'type' => "SOA",
			'content' => "ns1.best-hosting.cz. admin.best-hosting.cz. 2012080603 28800 7200 1209600 86400",
			'ttl' => "86400",
			'priority' => "0",
			'dnssec' => "0",
		);
		$data[] = array(
			'domainName' => "testdomain",
			'tld' => "cz",
			'name' => "testdomain.cz",
			'type' => "A",
			'content' => "194.8.253.1",
			'ttl' => "86400",
			'priority' => "0",
			'dnssec' => "0",
		);
		$data[] = array(
			'domainName' => "testdomain",
			'tld' => "cz",
			'name' => "www",
			'type' => "CNAME",
			'content' => "testdomain.cz.",
			'ttl' => "86400",
			'priority' => "0",
			'dnssec' => "0",
		);
		
				
		return $data;
	}
}