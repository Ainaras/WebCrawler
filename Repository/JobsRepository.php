<?php

namespace Ainars\WebCrawler\Repository;

use Ainars\WebCrawler\Model\Job;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;

class JobsRepository {

	protected Connection $db;

	protected string $tableName = 'import_jobs';

	public function __construct(Connection $db)
	{
		$this->db = $db;
	}

	/**
	 * @throws Exception
	 */
	public function get($initUrl): Job
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

	public function createChildJob(string $url, Job $job = null, string $initUrl = ''): bool
	{
		try {
			return (bool)$this->db->insert(
				$this->tableName, 
				[
					'url' => $url,
					'md5url' => md5($url),
					'init_md5url' => md5($initUrl),
					'parent_job_id' => $job ? $job->getId() : null
				]
			);
		} catch (UniqueConstraintViolationException $e) {
			return false;
		}
	}

	public function save(Job $job): bool
	{
		return $this->db->update(
			$this->tableName, 
			[
				'status' => $job->getStatus(),
			], 
			[
				'id' => $job->getId(),
			]
		);
	}

	public function getStatus($initUrl): array
	{
		$sql = 'SELECT `status`, count(*) as total
				FROM ' . $this->tableName . '
				WHERE init_md5url = :init_md5
				GROUP BY status';

		$data = $this->db->fetchAll($sql, [
			'init_md5' => md5($initUrl)
		]);

		$result = [
			Job::STATUS_NOT_IMPORTED => 0,
			Job::STATUS_READ => 0,
			Job::STATUS_IMPORTED => 0,
			Job::STATUS_ERROR => 0,
			Job::STATUS_SKIPPED => 0
		];
		foreach ($data as $row) {
			$result[$row['status']] = $row['total'];
		}

		return $result;
	}

}
