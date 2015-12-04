<?php 
namespace SynKnot\Application\Adapters;

use SynKnot\Exception\SynKnotException;

class BestHostingDNSRecordsMysqliAdapter extends AbstractDataAdapter{
	public function getData() {
		$data = array();

// 		try{
		$connection = mysqli_connect($this->config['server'], 
									$this->config['login'], 
									$this->config['password'], 
									$this->config['database']);
		
		if(!$connection){
			throw new SynKnotException(mysqli_error($connection));
		}
// 		}catch(Exception $e){
// 			die($e->getMessage());
// 		}
		
		//or throw new SynKnotException("Cannot connect to db: " . mysqli_errno($connection));
		
		$result = $connection->query('SELECT CONCAT(d.name, ".", d.tld) AS domainName, d.dnssec, r.name, r.type, r.content, r.ttl, r.priority ' .
			'FROM dns_record r, domain d, item i ' .
			'WHERE d.id = r.domain_id AND r.status = "active" ' .
			'AND i.id = d.id ' .
			'AND i.status IN ("waiting", "active") ' .
			'ORDER BY d.id, r.type, r.name, r.content;');

		if(!$result){
			throw new SynKnotException('Bad result: ' . mysqli_error($connection));
		}
		
		while ($row = mysqli_fetch_assoc($result)) {
			//název domény
			$data[$row['domainName']][] = $row;
		}
		
		return $data;
	}
}