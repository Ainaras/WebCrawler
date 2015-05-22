<?php

namespace Ainars\WebCrawler\Model;

class DefaultJobsBuilder implements JobsBuilderInterface
{

	/**
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * @param string $baseUrl
	 */
	public function __construct($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	/**
	 * @param \simple_html_dom $html
	 * @return array
	 */
	public function fromHtml(\simple_html_dom $html)
	{
		$newJobs = [];
		foreach ($this->_getLinkObjects($html) as $link) {
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
		return $this->_isHashUrl($url);
	}

	protected function _isHashUrl($url) {
		return strpos($url, '#') === 0;
	}

	protected function _isAbsolutUrl($url) {
		return strpos($url, 'http://') === 0 or
			strpos($url, '//') === 0 or
			strpos($url, 'https://') === 0;
	}

	protected function _buildAbsoluteUrl($url) {
		if ($this->_isAbsolutUrl($url)) {
			return $url;
		}

		return $this->baseUrl . $url;
	}
}
