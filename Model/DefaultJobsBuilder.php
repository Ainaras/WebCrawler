<?php

namespace Ainars\WebCrawler\Model;

use Ainars\WebCrawler\Contract\JobsBuilderInterface;
use Ainars\WebCrawler\Model\Job;
use GuzzleHttp\Url;
use simplehtmldom_1_5\simple_html_dom;

class DefaultJobsBuilder implements JobsBuilderInterface {

	/**
	 * current url
	 */
	protected Url $baseUrl;

	/**
	 * array of regexp to check absolute URL.
	 * If URL pass one of rules, then we skip this URL
	 * @var string[]
	 */
	protected $skipRules = [];

	/**
	 * sends back all urls of links in page
	 * @return string[]
	 */
	public function fromHtml(simple_html_dom $html, Job $job): array
	{
		$this->baseUrl = Url::fromString($job->getUrl());

		$newJobs = [];
		foreach ($this->_getLinkObjects($html) as $link) {
			if (empty($link->href)) {
				continue;
			}

			if ($this->_canWeSkip($link->href, $link->plaintext)) {
				continue;
			}

			$newJobs[] = $this->_buildAbsoluteUrl($link->href);
		}

		return $newJobs;
	}

	protected function _getLinkObjects(simple_html_dom $html)
	{
		return $html->find('a');
	}

	protected function _canWeSkip($url, $text = ''): bool
	{
		if ($this->_isHashUrl($url) || $this->_isMail($url)) {
			return true;
		}

		if (!empty($this->skipRules)) {
			$absoluteUrl = $this->_buildAbsoluteUrl($url);
			foreach ($this->skipRules as $rule) {
				if (preg_match($rule, $absoluteUrl)) {
					return true;
				}
			}
		}

		return false;
	}

	protected function _isHashUrl($url): bool
	{
		return strpos($url, '#') === 0;
	}

	protected function _isMail($url): bool
	{
		return strpos($url, 'mailto:') === 0;
	}

	protected function _buildAbsoluteUrl(string $url): string
	{
		return (string) $this->baseUrl->combine($url);
	}

}
