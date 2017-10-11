<?php 
use SynKnot\Application\FileBuilder;

class FileBuilderTest extends \PHPUnit_Framework_TestCase{
	public function testMoveDirectory(){
		$fb = new FileBuilder($this->getConfig());
		
		$tmpdir = '/tmp/synknot-file-builder-test/';
		$tmpdir2 = '/tmp/synknot-file-builder-test2/';
		$tmpfile = $tmpdir . 'file.zone';		
		$fb->mkdir($tmpdir);
		
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
}