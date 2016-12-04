<?php

namespace Ainars\WebCrawler\Model;

use Ainars\WebCrawler\Contract\LoggerInterface;

class DefaultLogger implements LoggerInterface
{
	public function log($severity, $message) {
		echo '[' . date('Y-m-d H:i:s') . '] ' . $severity . ' ' . $message . PHP_EOL;
	}

}
