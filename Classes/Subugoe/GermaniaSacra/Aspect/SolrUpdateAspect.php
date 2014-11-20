<?php
namespace Subugoe\GermaniaSacra\Aspect;


use Subugoe\GermaniaSacra\Queue\SolrUpdateJob;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Aspect
 */
class SolrUpdateAspect {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Jobqueue\Common\Job\JobManager
	 */
	protected $jobManager;

	/**
	 * @Flow\After("method(Subugoe\GermaniaSacra\Controller\.*->listAction())")
	 * return void
	 */
	public function createStaticFileWhenListMethodIsCalled() {
		$job = new SolrUpdateJob('solr');
		$this->jobManager->queue('solr', $job);
	}

}