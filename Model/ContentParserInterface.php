<?php

namespace Ainars\WebCrawler\Model;

interface ContentParserInterface {

	public function parse(\simple_html_dom $html, Job $job);

}