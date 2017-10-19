<?php 
use SynKnot\Application\FileBuilder;

/**
 * @group files
 * @author iki
 *
 */
class FileBuilderTest extends \PHPUnit_Framework_TestCase{
	public function testMoveDirectory(){
		$fb = new FileBuilder($this->getConfig());
		
		$tmpdir = $this->getTmpDir('tmp1');
		$tmpdirChecksum = $tmpdir . 'checksum/';
		$tmpdir2 = $this->getTmpDir('tmp2');
		$tmpfile = $tmpdir . 'file.zone';		
		$fb->mkdir($tmpdir);
		$fb->mkdir($tmpdirChecksum);
		
		$this->assertTrue(is_dir($tmpdir), sprintf('Directory %1$s does not exists', $tmpdir));
		file_put_contents($tmpfile, 'data');
		
		$fb->moveDirectory($tmpdir, $tmpdir2);
		$this->assertTrue(is_dir($tmpdir2), sprintf('Directory %1$s does not exists', $tmpdir2));
		$this->assertFalse(is_dir($tmpdir), sprintf('Directory %1$s does exists', $tmpdir));

		$fb->clearDirectory($tmpdir2);
		
// 		rmdir($tmpdir2);
		$this->assertFalse(is_dir($tmpdir2), sprintf('Directory %1$s does exists', $tmpdir2));
	}
	
	private function getConfig(){
		return parse_ini_file(__DIR__ . '/../../../config.ini');
	}
	
	private function getTmpDir($ident){
		$dir = sprintf('/tmp/synknot-file-builder-%1$s-%2$s/', $ident, rand(10000, 1000000));
		if(is_dir($dir)){
			$dir = $this->getTmpDir();
		}
		return $dir;
	}
}