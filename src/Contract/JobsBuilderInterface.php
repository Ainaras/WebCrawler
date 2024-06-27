<?php

namespace Ainars\WebCrawler\Contract;

use Ainars\WebCrawler\Model\Job;
use simplehtmldom_1_5\simple_html_dom;

interface JobsBuilderInterface {

	/**
	 * @return string[] Array of Urls
	 */
	public function fromHtml(simple_html_dom $html, Job $job): array;

}
