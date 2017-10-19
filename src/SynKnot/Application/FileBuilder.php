<?php 
namespace SynKnot\Application;

use SynKnot\Exception\SynKnotException;

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
		$pathChecksum = $path . 'checksum/';
		$pathTimers = $path . 'timers/';
		
		if(is_null($patterns)){
			$patterns = array('*.zone', '*.checksum', '*.mdb');
		}
		
		$pathPatterns = array();
		foreach ($patterns as $pattern){
			$pathPatterns[] = $path . $pattern;
			$pathPatterns[] = $pathChecksum . $pattern;
			$pathPatterns[] = $pathTimers . $pattern;
		}
// 		if(is_dir($path)){
// var_dump($path);
// 		var_dump($patterns);
		if(file_exists($path)){
			foreach (glob("{" . implode(",", $pathPatterns) . "}", GLOB_BRACE) as $filename) {
				if (is_file($filename)) {
// 					var_dump($filename);
// 					var_dump('unlink ' . $filename);
					unlink($filename);
				}
			}
			
			//checksum directory
			if(is_dir($pathChecksum)){
				$this->testEmptyDir($pathChecksum);
				rmdir($pathChecksum);
			}

			//timers directory
			if(is_dir($pathTimers)){
				$this->testEmptyDir($pathTimers);
				rmdir($pathTimers);
			}
			
			$this->testEmptyDir($path);
			rmdir($path);
// 			var_dump($path);
			
// 			$this->moveDirectory($path, "/tmp/knot" . sha1(rand(100000, 1000000)));
		}
// 		else{
// 			$this->mkdir($path);
// 		}
	}
	
	public function moveDirectory($source, $destination){
		$this->testValidDir($source);
		$this->testValidDir($destination);
// 		var_dump($destination);
		//$source = "/tmp/knot/pri-tmp/"
		//$destination = "/tmp/knot/ptr/"
		
		//kontrola zdrojového adresáře
		if(file_exists($source)){
			//kontrola cílového adresáře
			if(file_exists($destination)){
				//vytvoreni adresare pro checksum
				$destinationChecksumDir = $destination . 'checksum/';
				if(!is_dir($destinationChecksumDir)){
					mkdir($destinationChecksumDir);
				}
				
				foreach (glob($source . "*") as $filename) {
					//pokud nejsou souubory změněny, tak je nepřepisovat
					$destinationFile = $destination . basename($filename); 
					$destinationFileCheckSum = $destinationChecksumDir . basename($filename) . ".checksum"; 

					//pokud existuje zdrojový soubor
					if(file_exists($filename)){
						//die($filename); //	/tmp/knot/pri-tmp/1000webu.cz.zone
						
						//existuje vůbec původní?
						if(file_exists($destinationFile)){ //		/tmp/knot/pri-tmp/1000webu.cz.zone
							//die($destinationFile . PHP_EOL); 
							//existuje cílový soubor a je zdrojový a cílový podle checksum rozdílný?
							if(!is_file($destinationFileCheckSum) || md5_file($filename) != file_get_contents($destinationFileCheckSum)){
								//přepiš zdrojový na cílový
								rename($filename, $destinationFile);
								//unlink() - smazat checksum zdrojový
								//vytvoř checksum
								$this->saveContent(md5_file($destinationFile), $destinationFileCheckSum);
							}else{
								//jsou stejné, tak unlink toho starého
								unlink($filename);
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
				//cilovy adresar neexistuje, tak je mozne ho rovnou presunout
				rename($source, $destination);
			}
		}
// 		$this->mkdir($source);
		//cilovy adresar neexistuje, tak ho vytvorime
		$this->mkdir($destination); // ?
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
	
	public function symlink($target, $link){
		if(!file_exists($target) && !is_dir($target)){
			throw new SynKnotException(sprintf('File or directory "%1$s" not found', $target));
		}

		if(file_exists($link) || is_dir($link)){
			//skipping
			return;
		}
		
		symlink($target, $link);
	}
	
	private function testValidDir($dir){
		if(substr($dir, -1) != '/'){
			throw new SynKnotException(sprintf('Directory path has to end with slash %1$s', $dir));
		}
	}
	
	private function testEmptyDir($dir){
		if(!is_dir($dir)){
			throw new SynKnotException(sprintf('Directory does not exist %1$s', $dir));
		}
		
		if(count(scandir($dir)) != 2){
			throw new SynKnotException(sprintf('Directory is not empty %1$s', $dir));
		}
	}
}