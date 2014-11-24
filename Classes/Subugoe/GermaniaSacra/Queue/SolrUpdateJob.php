<?php
namespace Subugoe\GermaniaSacra\Queue;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Ingo Pfennigstorf <pfennigstorf@sub-goettingen.de>
 *      Goettingen State Library
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use Subugoe\GermaniaSacra\Controller\DataExportController;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Log\LoggerFactory;
use TYPO3\Jobqueue\Common\Job\JobInterface;
use TYPO3\Jobqueue\Common\Queue\Message;
use TYPO3\Jobqueue\Common\Queue\QueueInterface;

ini_set('memory_limit', '-1');


/**
 * Queue for updating the solr index
 */
class SolrUpdateJob implements JobInterface {

	/**
	 * @var \TYPO3\Flow\Log\Logger
	 * @Flow\Inject
	 */
	protected $logger;


	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param $settings
	 */
	public function injectSettings($settings) {
		$this->settings = $settings;
	}

	/**
	 * Execute the job
	 *
	 * A job should finish itself after successful execution using the queue methods.
	 *
	 * @param QueueInterface $queue
	 * @param Message $message The original message
	 * @return boolean TRUE if the job was executed successfully and the message should be finished
	 */
	public function execute(QueueInterface $queue, Message $message) {

		$solrExporter = new DataExportController();
		$solrExporter->injectSettings($this->settings);
		$solrExporter->initializeAction();

		try {
			$jobExecuted = $solrExporter->mysql2solrExportAction();
		} catch (\Exception $e) {
			$this->logger->logException($e);
			$jobExecuted = FALSE;
		}
		return $jobExecuted;
	}

	/**
	 * Get an optional identifier for the job
	 *
	 * @return string A job identifier
	 */
	public function getIdentifier() {
		return 'solr';
	}

	/**
	 * Get a readable label for the job
	 *
	 * @return string A label for the job
	 */
	public function getLabel() {
		return 'Solr Update Queue';
	}
}
