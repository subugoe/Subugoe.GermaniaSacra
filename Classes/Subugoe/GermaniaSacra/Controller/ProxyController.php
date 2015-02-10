<?php
namespace Subugoe\GermaniaSacra\Controller;

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
use Subugoe\GermaniaSacra\Utility\JsonGeneratorUtility;
use TYPO3\Flow\Error\Exception;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Mvc\Controller\RestController;
use TYPO3\Flow\Annotations as Flow;

/**
 * Proxy for remote origins
 */
class ProxyController extends AbstractBaseController {

	/**
	 * @var string
	 */
	protected $geoJsonUrl;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\Browser
	 */
	protected $browser;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\CurlEngine
	 */
	protected $browserRequestEngine;

	/**
	 * Initializes defaults
	 */
	public function initializeAction() {
		parent::initializeAction();
		$this->browser->setRequestEngine($this->browserRequestEngine);
	}

	/**
	 * @return \Guzzle\Http\EntityBodyInterface|string
	 */
	public function geoJsonAction() {
		$geoJsonUrl = $this->settings['data']['geoJson'];
		$this->initializeAction();

		if ($this->cacheInterface->has('geoJson')) {
			return $this->cacheInterface->get('geoJson');
		}
		try {
			$geoJson = $this->browser->request($geoJsonUrl)->getContent();
			$this->cacheInterface->set('geoJson', $geoJson);
			return $geoJson;
		} catch (\Exception $e) {
			$this->systemLogger->logException($e);
			return '';
		}
	}

	/**
	 * @return \Guzzle\Http\EntityBodyInterface|string
	 */
	public function literatureAction() {
		$literatureUrl = $this->settings['data']['literature'];
		$this->initializeAction();

		try {
			$literature = $this->browser->request($literatureUrl)->getContent();
			return $literature;
		} catch (\Exception $e) {
			$this->systemLogger->logException($e);
			return '';
		}
	}

	/**
	 * @param string $entityName
	 */
	public function entityAction($entityName) {

		$entityName = filter_var($entityName, FILTER_SANITIZE_STRING);

		$controller = ucfirst($entityName) . 'Controller';
		$controllerName = '\\Subugoe\\GermaniaSacra\\Controller\\' . $controller;

		$this->response->setHeader('Content-Type', 'application/json');

		if (!class_exists($controllerName)) {
			throw new Exception('Class ' . $controllerName . ' not found.', 1409817407);
		}

		/** @var \TYPO3\Flow\Mvc\Controller\ActionController $controllerInstance */
		$controllerInstance = new $controllerName();
		$controllerInstance->initializeAction();

		if ($this->cacheInterface->has($entityName)) {
			return $this->cacheInterface->get($entityName);
		} else {
			if ($entityName === 'kloster') {
				return $controllerInstance->allAsJson();
			} else {
				$this->forward('list', $entityName, 'Subugoe.GermaniaSacra', array('format' => 'json'));
			}
		}
	}
}
