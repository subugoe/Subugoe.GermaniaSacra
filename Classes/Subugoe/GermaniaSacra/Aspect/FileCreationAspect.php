<?php
namespace Subugoe\GermaniaSacra\Aspect;

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

use Subugoe\GermaniaSacra\Queue\FileGenerationJob;
use Subugoe\GermaniaSacra\Utility\EntityUtility;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;

/**
 * Creates static files after certain actions
 * @Flow\Aspect
 */
class FileCreationAspect {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Jobqueue\Common\Job\JobManager
	 */
	protected $jobManager;

	/**
	 * @Flow\After("method(Subugoe\GermaniaSacra\Controller\LandController->editAction())")
	 * return void
	 */
	public function createStaticFileWhenListMethodIsCalled() {
		$jsonJob = new FileGenerationJob('kloster');
		$this->jobManager->queue('kloster', $jsonJob);
	}

	/**
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $jointPoint
	 * @Flow\After("method(Subugoe\GermaniaSacra\Controller\.*->listAction())")
	 * return void
	 */
	public function createStaticFileWhenListMethodIsCalledInEntities(JoinPointInterface $jointPoint) {
		$entityName = EntityUtility::getEntityNameFromControllerClassName($jointPoint->getClassName());
		$jsonJob = new FileGenerationJob($entityName);
		$this->jobManager->queue('kloster', $jsonJob);
	}

}
