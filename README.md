# WebCrawler

## Example

	$builder = new \Custom\JobsBuilder();
	$parser = new \Custom\SiteParser();

	$worker = new Worker($doctrineDbal, $builder, $parser);
	$worker->setInitUrl($basisUrl);

	try {
		$job = $worker->doJob();
	} catch (\ImporterException $ex) {
		return $ex->getMessage();
	}

	return $job->getStatus();