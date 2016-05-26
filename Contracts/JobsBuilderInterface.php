<?php

namespace Ainars\WebCrawler\Contract;

interface JobsBuilderInterface {

	/**
	 * @param \simple_html_dom $html
	 * @return array Array of Urls
	 */
	public function fromHtml(\simple_html_dom $html);

}