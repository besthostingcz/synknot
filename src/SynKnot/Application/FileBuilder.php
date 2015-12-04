<?php 
namespace SynKnot\Application;

class FileBuilder{
	private $config;
	
	public function __construct(array $config){
		$this->config = $config;
	}
	
	public function moveFile($oldPath, $newPath){
		if(file_exists($oldPath)){
			rename($oldPath, $newPath);
		}
	}
	

	public function saveContent($content, $path){
		$user = $this->config['file-user'];
		$group = $this->config['file-group'];
	
		$dirname = dirname($path);
		if(!is_dir($dirname)){
			$this->mkdir($dirname);
		}
	
		file_put_contents($path, $content);
		chown($path, $user);
		chgrp($path, $group);
	}

	public function clearDirectory($path, $patterns = null){
		if(is_null($patterns)){
			$patterns = array('*.zone', '*.checksum');
		}
		$pathPatterns = array();
		foreach ($patterns as $pattern){
			$pathPatterns[] = $path . $pattern;
		}
// 		if(is_dir($path)){
// var_dump($path);
		if(file_exists($path)){
			foreach (glob("{" . implode(",", $pathPatterns) . "}", GLOB_BRACE) as $filename) {
				if (is_file($filename)) {
// 					var_dump($filename);
					unlink($filename);
				}
			}
			rmdir($path);
// 			var_dump($path);
			
// 			$this->moveDirectory($path, "/tmp/knot" . sha1(rand(100000, 1000000)));
		}else{
			$this->mkdir($path);
		}
	}
	
	public function moveDirectory($source, $destination){
// 		var_dump($destination);
		//$source = "/tmp/knot/pri-tmp/"
		//$destination = "/tmp/knot/ptr/"
		
		//kontrola zdrojového adresáře
		if(file_exists($source)){
			//kontrola cílového adresáře
			if(file_exists($destination)){
				foreach (glob($source . "*") as $filename) {
					//pokud nejsou souubory změněny, tak je nepřepisovat
					$destinationFile = $destination . basename($filename); 
					$destinationFileCheckSum = $destinationFile . ".checksum"; 
					//existuje zdrojový soubor
					if(file_exists($filename)){
						//die($filename); //	/tmp/knot/pri-tmp/1000webu.cz.zone
						
						//existuje vůbec původní?
						if(file_exists($destinationFileCheckSum)){ //		/tmp/knot/pri-tmp/1000webu.cz.zone
							//die($destinationFile . PHP_EOL); 
							//jsou oba stejné - rozdílné?
							if(md5_file($filename) != file_get_contents($destinationFileCheckSum)){
								rename($filename, $destinationFile);
								$this->saveContent(md5_file($destinationFile), $destinationFileCheckSum);
							}else{
								unlink($filename);
								//jsou stejné, tak unlink toho nového
							}
						}else{
							//nemusí existovat cílový a stejně se přepíše
							rename($filename, $destinationFile);
							$this->saveContent(md5_file($destinationFile), $destinationFileCheckSum);
						}
					}//else neexistuje zdrojový soubor, tak těžko něco dělat
// 						unlink($filename);
				}
			}else{
// 				var_dump(array($source, $destination));
				//
				rename($source, $destination);
			}
		}
		$this->mkdir($source);
// 		$this->mkdir($destination); // ?
	}
	
	public function mkdir($path, $rights = 0770, $recure = true){
		if(!file_exists($path)){
			mkdir($path, $rights, $recure);
		}
		$user = $this->config['file-user'];
		$group = $this->config['file-group'];
		chown($path, $user);
		chgrp($path, $group);
	}
}