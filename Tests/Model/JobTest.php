<?php

use Ainars\WebCrawler\Model\Job;

class JobTest extends PHPUnit\Framework\TestCase {

	public function testGetId()
	{
		$job = new Job('u', '1', Job::STATUS_NOT_IMPORTED);
		$this->assertEquals('1', $job->getId());

		$job2 = new Job('u', 2, Job::STATUS_NOT_IMPORTED);
		$this->assertEquals(2, $job2->getId());
	}

	public function testGetUrl()
	{
		$job = new Job('u', '1', Job::STATUS_NOT_IMPORTED);
		$this->assertEquals('u', $job->getUrl());

		$job2 = new Job('u', 2, Job::STATUS_NOT_IMPORTED);
		$this->assertEquals('u', $job2->getUrl());

		$job3 = new Job('u3', 2, Job::STATUS_NOT_IMPORTED);
		$this->assertEquals('u3', $job3->getUrl());
	}

	public function testDone()
	{
		$job = new Job('u', '1', Job::STATUS_NOT_IMPORTED);
		$this->assertFalse($job->isDone());
		$this->assertEquals(Job::STATUS_NOT_IMPORTED, $job->getStatus());

		$job = new Job('u', '1', Job::STATUS_IMPORTED);
		$this->assertTrue($job->isDone());
		$this->assertEquals(Job::STATUS_IMPORTED, $job->getStatus());
	}

	public function testSetStatus()
	{
		$job = new Job('u', '1', Job::STATUS_NOT_IMPORTED);
		$job->setStatus(Job::STATUS_IMPORTED);

		$this->assertTrue($job->isDone());
		$this->assertEquals(Job::STATUS_IMPORTED, $job->getStatus());

		$job->setStatus(Job::STATUS_ERROR);

		$this->assertFalse($job->isDone());
		$this->assertEquals(Job::STATUS_ERROR, $job->getStatus());
	}

}
