<?php

namespace Ainars\WebCrawler\Model;

use Exception;
use GuzzleHttp\Client;
use Doctrine\DBAL\Connection;
use Sunra\PhpSimple\HtmlDomParser;
use Ainars\WebCrawler\Exception\SkipException;
use Ainars\WebCrawler\Repository\JobsRepository;
use Ainars\WebCrawler\Contract\JobsBuilderInterface;
use Ainars\WebCrawler\Contract\ContentParserInterface;

/**
 * @author ainars
 */
class Worker
{

	/**
	 * @var Connection
	 */
	protected $db;

	/**
	 * @var Job
	 */
	protected $currentJob;

	/**
	 * @var JobsRepository
	 */
	protected $jobsRepo;

	/**
	 * @var JobsBuilderInterface
	 */
	protected $nextJobsBuilder;

	/**
	 * @var ContentParserInterface
	 */
	protected $contentParser;

	/**
	 * @var string
	 */
	protected $initUrl;

	public function __construct(
		Connection $db,
		JobsBuilderInterface $builder,
		ContentParserInterface $parser)
	{
		$this->db = $db;
		$this->nextJobsBuilder = $builder;
		$this->contentParser = $parser;

		$this->jobsRepo = new JobsRepository($this->db);
	}

	/**
	 * We need this URL only at begin, at first run
	 *
	 * @param string $initUrl
	 */
	public function setInitUrl($initUrl) {
		$this->initUrl = $initUrl;

		$this->jobsRepo->createChildJob($initUrl, null, $initUrl);
	}

	public function doJob()
	{
		$this->currentJob = $this->jobsRepo->get($this->initUrl);

		try {
			$this->_craw();

			$html = HtmlDomParser::str_get_html($this->currentJob->getHtml());
			if (!$html) {
				throw new SkipException('no html is generated');
			}

			foreach ($this->getNextJobsBuilder()->fromHtml($html, $this->currentJob) as $todoUrl) {
				$this->jobsRepo->createChildJob($todoUrl, $this->currentJob, $this->initUrl);
			}

			if ($this->contentParser->parse($html, $this->currentJob)) {
				$this->currentJob->setStatus(Job::STATUS_IMPORTED);
			} else {
				$this->currentJob->setStatus(Job::STATUS_READ);
			}

		} catch (SkipException $ex) {
			$this->currentJob->setStatus(Job::STATUS_SKIPPED);
		} catch (Exception $ex) {
			$this->currentJob->setStatus(Job::STATUS_ERROR);
		}

		$this->jobsRepo->save($this->currentJob);

		return $this->currentJob;
	}

	protected function _craw()
	{
		$client = new Client();
		$response = $client->get(
			$this->currentJob->getUrl()
			, array(
				'verify' => false,
				'timeout' => 10
			)
		);

		$this->currentJob->setHtml($response->getBody(true));
	}

	/**
	 * @return JobsBuilderInterface
	 */
	protected function getNextJobsBuilder() {
		return $this->nextJobsBuilder;
	}
}
