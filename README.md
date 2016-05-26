# WebCrawler

## Example that saves pages to disc

    $basisUrl = 'https://example.com';

	$builder = new \Ainars\WebCrawler\Model\DefaultJobsBuilder($basisUrl);
	$parser = new \Ainars\WebCrawler\Parser\SavePageParser(__DIR__);

	$worker = new Worker($doctrineDbal, $builder, $parser);
	$worker->setInitUrl($basisUrl);

	try {
		$job = $worker->doJob();
	} catch (\ImporterException $ex) {
		return $ex->getMessage();
	}

	return $job->getStatus();