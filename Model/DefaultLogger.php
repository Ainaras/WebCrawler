<?php

namespace Ainars\WebCrawler\Model;

use Psr\Log\AbstractLogger;

class DefaultLogger extends AbstractLogger
{
	public function log($level, $message, array $context = array()) {
		echo '[' . date('Y-m-d H:i:s') . '] ' . $level . ' ' . $message . PHP_EOL;
	}

}
