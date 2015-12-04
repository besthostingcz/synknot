<?php
namespace SynKnot\Application\Adapters;

use SynKnot\Exception\SynKnotException;
class BestHostingPTRAdapter extends AbstractDataAdapter{
	public function getData(){
		$data = array();

		$curl = curl_init();
		$user = $this->config['ptr-user'];
		$password = $this->config['ptr-password'];
		$url = $this->config['ptr-url'];
		
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $password);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		
		if(curl_errno($curl)){
			curl_close($curl);
			throw new SynKnotException(curl_error($curl));
		}
		
		$result = curl_exec($curl);
		
		curl_close($curl);
		
		$resultObjects = json_decode($result);
	
 		if(!is_array($resultObjects)){
 			throw new SynKnotException("Corrupted PTR data");
 		}
 			
		foreach ($resultObjects as $o){
			$data[] = (array) $o;
		}
		
		return $data;
	}
}