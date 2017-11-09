<?php

use Ainars\WebCrawler\Model\Job;
use Ainars\WebCrawler\Repository\JobsRepository;
use Doctrine\DBAL\DriverManager;

class JobsRepositoryTest extends PHPUnit_Framework_TestCase {

	protected $db;

	protected function setUp()
	{
		parent::setUp();

		$dbpath = __DIR__ . '/../test.sqlite';
		if (file_exists($dbpath)) {
			unlink($dbpath);
		}

		$data = [
			'path' => $dbpath,
			'driver' => 'pdo_sqlite'
		];
		$this->db = DriverManager::getConnection($data);

		$this->db->exec("CREATE TABLE IF NOT EXISTS `import_jobs` (
				`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				`parent_job_id`	INTEGER,
				`init_md5url`	TEXT NOT NULL,
				`md5url`	TEXT NOT NULL UNIQUE,
				`url`	TEXT NOT NULL,
				`status`	INTEGER NOT NULL DEFAULT 0,
				`created`	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				FOREIGN KEY(`parent_job_id`) REFERENCES `import_jobs`(`id`));
			CREATE INDEX IF NOT EXISTS FK_imported_imported on import_jobs (parent_job_id);"
		);

		$url = 'u';
		$this->db->insert('import_jobs', array(
			'url' => $url,
			'md5url' => md5($url),
			'init_md5url' => md5($url),
			'parent_job_id' => null
		));
	}

	protected function tearDown()
	{
		parent::tearDown();

		$this->db->close();
	}

	public function testGet()
	{
		$repo = new JobsRepository($this->db);

		$result = $repo->get('u');
		$this->assertInstanceOf(Job::class, $result);
		$this->assertEquals('u', $result->getUrl());
		$this->assertEquals(Job::STATUS_NOT_IMPORTED, $result->getStatus());
	}

	public function testGetNonExisting()
	{
		$repo = new JobsRepository($this->db);

		try {
			$repo->get('b');
			$this->fail('we expected exception');
		} catch (Exception $ex) {
			$this->assertInstanceOf(Exception::class, $ex);
		}
	}

	public function testGetStatus() {
		$url = 'b';
		$this->db->insert('import_jobs', array(
			'url' => $url,
			'md5url' => md5($url),
			'init_md5url' => md5($url),
			'status' => 2,
			'parent_job_id' => null
		));

		$repo = new JobsRepository($this->db);

		$result = $repo->getStatus('u');
		$this->assertEquals(array_sum($result), 1);
		$result = $repo->getStatus('b');
		$this->assertEquals(array_sum($result), 1);
		$result = $repo->getStatus('c');
		$this->assertEquals(array_sum($result), 0);
	}

}
