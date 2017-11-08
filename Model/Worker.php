<?php

namespace Ainars\WebCrawler\Model;

use Ainars\WebCrawler\Contract\ContentParserInterface;
use Ainars\WebCrawler\Contract\JobsBuilderInterface;
use Ainars\WebCrawler\Exception\SkipException;
use Ainars\WebCrawler\Repository\JobsRepository;
use Doctrine\DBAL\Connection;
use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Sunra\PhpSimple\HtmlDomParser;

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

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	public function __construct(
		Connection $db,
		JobsBuilderInterface $builder,
		ContentParserInterface $parser)
	{
		$this->db = $db;
		$this->nextJobsBuilder = $builder;
		$this->contentParser = $parser;

		$this->jobsRepo = new JobsRepository($this->db);
		$this->logger = new DefaultLogger();
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

	/**
	 * door to reset default logger
	 * @param LoggerInterface $logger
	 */
	function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function doJob()
	{
		$this->currentJob = $this->jobsRepo->get($this->initUrl);

		$this->logger->info('Start of: ' . $this->currentJob->getUrl());

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
			$this->logger->info($ex->toLogString());
		} catch (Exception $ex) {
			$this->currentJob->setStatus(Job::STATUS_ERROR);
			$this->logger->error($ex->getMessage() . ' in ' .
				$ex->getFile() . ' on ' .
				$ex->getLine() . ' line.');
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
