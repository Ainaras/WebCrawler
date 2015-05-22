<?php

namespace Ainars\WebCrawler\Repository;

use Ainars\WebCrawler\Model\Job;

class JobsRepository
{

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $tableName = 'import_jobs';

	/**
	 * @param \Doctrine\DBAL\Connection $db
	 */
	public function __construct(\Doctrine\DBAL\Connection $db)
	{
		$this->db = $db;
	}

	/**
	 * @return Job
	 * @throws \Exception
	 */
	public function get()
	{
		$data = $this->db->fetchAssoc(
			'SELECT *
				FROM ' . $this->tableName . '
				WHERE `status` = ' . Job::STATUS_NOT_IMPORTED . '
				LIMIT 1');

		if (empty($data)) {
			throw new \Exception('All jobs are done!');
		}

		return new Job($data['url'], $data['id'], $data['status']);
	}

	/**
	 * @param string $url
	 * @param Job $job
	 * @return boolean
	 */
	public function createChildJob($url, Job $job = null)
	{
		try {
			return $this->db->insert($this->tableName, array(
					'url' => $url,
					'md5url' => md5($url),
					'parent_job_id' => $job ? $job->getId() : null
			));
		} catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
			return false;
		}
	}

	/**
	 * @param Job $job
	 * @return boolean
	 */
	public function save(Job $job)
	{
		return $this->db->update($this->tableName, array(
				'status' => $job->getStatus()
				), ['id' => $job->getId()]);
	}

}
