<?php

namespace Ainars\WebCrawler\Model;

class Job
{

	const STATUS_NOT_IMPORTED = 0;
	const STATUS_READ = 1;
	const STATUS_IMPORTED = 2;
	const STATUS_ERROR = 3;
	const STATUS_SKIPPED = 4;

	protected string $html = '';

	public function __construct(
		protected string $url, 
		protected int $id, 
		protected int $status,
	) {}

	public function getId(): int
	{
		return $this->id;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function isDone(): bool
	{
		return $this->status == self::STATUS_IMPORTED;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function setStatus(int $status): void
	{
		$this->status = $status;
	}

	public function setHtml(string $html): void
	{
		$this->html = $html;
	}

	public function getHtml(): string
	{
		return $this->html;
	}

}
