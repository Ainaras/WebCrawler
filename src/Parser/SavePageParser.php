<?php

namespace Ainars\WebCrawler\Parser;

use Ainars\WebCrawler\Contract\ContentParserInterface;
use Ainars\WebCrawler\Model\Job;
use simplehtmldom_1_5\simple_html_dom;

/**
 * It is only a example.
 * Saves crawled page as html on given folder
 */
class SavePageParser implements ContentParserInterface
{

    /**
     * Base dir where to save page
     */
    protected string $_baseDir;

    function __construct(string $baseDir)
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
