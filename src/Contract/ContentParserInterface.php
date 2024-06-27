<?php

namespace Ainars\WebCrawler\Contract;

use Ainars\WebCrawler\Model\Job;
use simple_html_dom\simple_html_dom;

interface ContentParserInterface {

	public function parse(simple_html_dom $html, Job $job);

}