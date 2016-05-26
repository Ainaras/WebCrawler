<?php

namespace Ainars\WebCrawler\Contract;

use Ainars\WebCrawler\Model\Job;

interface ContentParserInterface {

	public function parse(\simple_html_dom $html, Job $job);

}