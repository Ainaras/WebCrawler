<?php

namespace Ainars\WebCrawler\Repository;

use Ainars\WebCrawler\Model\Job;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;

class JobsRepository {

	/**
	 * @var Connection
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $tableName = 'import_jobs';

	/**
	 * @param Connection $db
	 */
	public function __construct(Connection $db)
	{
		$this->db = $db;
	}

	/**
	 * @return Job
	 * @throws Exception
	 */
	public function get($initUrl)
	{
		$sql = 'SELECT *
				FROM ' . $this->tableName . '
				WHERE `status` = :status
					AND init_md5url = :init_md5
				LIMIT 1';

		$data = $this->db->fetchAssoc($sql, [
			'status' => Job::STATUS_NOT_IMPORTED,
			'init_md5' => md5($initUrl)
		]);

		if (empty($data)) {
			throw new Exception('All jobs are done!');
		}

		return new Job($data['url'], $data['id'], $data['status']);
	}

	/**
	 * @param string $url
	 * @param Job $job
	 * @param string $initUrl
	 * @return boolean
	 */
	public function createChildJob($url, Job $job = null, $initUrl)
	{
		try {
			return $this->db->insert($this->tableName, array(
						'url' => $url,
						'md5url' => md5($url),
						'init_md5url' => md5($initUrl),
						'parent_job_id' => $job ? $job->getId() : null
			));
		} catch (UniqueConstraintViolationException $e) {
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
