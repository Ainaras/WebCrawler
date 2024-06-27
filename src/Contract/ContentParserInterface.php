<?php

namespace Ainars\WebCrawler\Contract;

use Ainars\WebCrawler\Model\Job;
use simplehtmldom_1_5\simple_html_dom;

interface ContentParserInterface {

	public function parse(simple_html_dom $html, Job $job);

}