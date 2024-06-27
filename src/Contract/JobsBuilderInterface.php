<?php

namespace Ainars\WebCrawler\Contract;

use Ainars\WebCrawler\Model\Job;
use simple_html_dom\simple_html_dom;

interface JobsBuilderInterface {

	/**
	 * @return string[] Array of Urls
	 */
	public function fromHtml(simple_html_dom $html, Job $job): array;

}
