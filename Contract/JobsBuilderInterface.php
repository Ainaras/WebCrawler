<?php

namespace Ainars\WebCrawler\Contract;

use Ainars\WebCrawler\Model\Job;

interface JobsBuilderInterface {

	/**
	 * @param \simple_html_dom $html
     * @param Job $job
	 * @return array Array of Urls
	 */
	public function fromHtml(\simple_html_dom $html, Job $job);

}
