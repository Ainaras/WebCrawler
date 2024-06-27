<?php

namespace Ainars\WebCrawler\Exception;

class SkipException extends \RuntimeException
{

	public function toLogString(): string {
		return 'Skipped because: ' .
				$this->getMessage() . ' in ' .
				basename($this->getFile()) . ' on ' .
				$this->getLine() . ' line.';
	}

}
