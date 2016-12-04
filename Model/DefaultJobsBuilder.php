<?php

namespace Ainars\WebCrawler\Model;

use Ainars\WebCrawler\Contract\JobsBuilderInterface;
use Ainars\WebCrawler\Model\Job;
use GuzzleHttp\Url;
use simplehtmldom_1_5\simple_html_dom;

class DefaultJobsBuilder implements JobsBuilderInterface
{

	/**
	 * @var Url
	 */
	protected $baseUrl;

	/**
	 * sends back all urls of links in page
     *
	 * @param simple_html_dom $html
	 * @return array
	 */
	public function fromHtml(simple_html_dom $html, Job $job)
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

	protected function _getLinkObjects($html) {
		return $html->find('a');
	}

	protected function _canWeSkip($url, $text = '') {
		return $this->_isHashUrl($url) || $this->_isMail($url);
	}

	protected function _isHashUrl($url) {
		return strpos($url, '#') === 0;
	}

	protected function _isMail($url) {
		return strpos($url, 'mailto:') === 0;
	}

	protected function _buildAbsoluteUrl($url) {
		return (string)$this->baseUrl->combine($url);
	}

}
