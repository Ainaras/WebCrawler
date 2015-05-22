<?php

namespace Ainars\WebCrawler\Model;

class Job
{

	const STATUS_NOT_IMPORTED = 0;
	const STATUS_READ = 1;
	const STATUS_IMPORTED = 2;
	const STATUS_ERROR = 3;
	const STATUS_SKIPPED = 4;

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $html;

	/**
	 * @param string $url
	 * @param int $id
	 * @param int $status
	 */
	public function __construct($url, $id, $status)
	{
		$this->url = $url;
		$this->id = $id;
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return bool
	 */
	public function isDone()
	{
		return $this->status == self::STATUS_IMPORTED;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param int $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @param string $html
	 */
	public function setHtml($html)
	{
		$this->html = $html;
	}

	/**
	 * @return string
	 */
	public function getHtml()
	{
		return $this->html;
	}

}
