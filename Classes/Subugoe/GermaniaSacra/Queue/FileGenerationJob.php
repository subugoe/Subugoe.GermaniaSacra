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

use TYPO3\Flow\Annotations as Flow;
use Flowpack\Jobqueue\Common\Job\JobInterface;
use Flowpack\Jobqueue\Common\Queue\Message;
use Flowpack\Jobqueue\Common\Queue\QueueInterface;

/**
 * Queue for generating json files
 */
class FileGenerationJob implements JobInterface
{
    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var \Subugoe\GermaniaSacra\Service\JsonGeneratorService
     * @Flow\Inject
     */
    protected $jsonGeneratorService;

    /**
     * @var \TYPO3\Flow\Log\Logger
     * @Flow\Inject
     */
    protected $logger;

    public function __construct($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * Execute the job
     *
     * A job should finish itself after successful execution using the queue methods.
     *
     * @param QueueInterface $queue
     * @param Message $message The original message
     * @return bool TRUE if the job was executed successfully and the message should be finished
     */
    public function execute(QueueInterface $queue, Message $message)
    {
        $jobExecuted = false;

        try {
            $jobExecuted = $this->jsonGeneratorService->generateJsonFile($this->entityName);
        } catch (\Exception $e) {
            $this->logger->logException($e);
        }
        return $jobExecuted;
    }

    /**
     * Get an optional identifier for the job
     *
     * @return string A job identifier
     */
    public function getIdentifier()
    {
        return $this->entityName;
    }

    /**
     * Get a readable label for the job
     *
     * @return string A label for the job
     */
    public function getLabel()
    {
        return 'jsonjob';
    }
}
