<?php

namespace Ainars\WebCrawler\Parser;

use simple_html_dom;
use Ainars\WebCrawler\Model\Job;
use Ainars\WebCrawler\Contract\ContentParserInterface;

/**
 * It is only a example.
 * Saves crawled page as html on given folder
 */
class SavePageParser implements ContentParserInterface
{

    /**
     * Base dir where to save page
     *
     * @var string
     */
    protected $_baseDir;

    function __construct($baseDir)
    {
        $this->_baseDir = $baseDir;
    }

    public function parse(simple_html_dom $html, Job $job)
    {
        $filename = $this->_baseDir . DIRECTORY_SEPARATOR .
                date('Y-m-d_H.i.s') . DIRECTORY_SEPARATOR .
                md5($job->getUrl()) . '.html';
        return file_put_contents($filename, $job->getHtml());
    }

}
