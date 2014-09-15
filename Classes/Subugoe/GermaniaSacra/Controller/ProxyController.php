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
class ProxyController extends ActionController {

	/**
	 * @var string
	 */
	protected $geoJsonUrl;

	/**
	 * @var \Guzzle\Http\Client
	 */
	protected $client;

	/**
	 * Initializes defaults
	 */
	public function initializeAction() {
		$this->client = new \Guzzle\Http\Client();
	}

	/**
	 * @return \Guzzle\Http\EntityBodyInterface|string
	 */
	public function geoJsonAction() {
		$geoJsonUrl = $this->settings['data']['geoJson'];
		$request = $this->client->get($geoJsonUrl);
		$response = $request->send();
		if ($response->getBody()) {
			$geoJson = $response->getBody();
			return $geoJson;
		}
		return '';
	}

	/**
	 * @return \Guzzle\Http\EntityBodyInterface|string
	 */
	public function literatureAction() {
		$literatureUrl = $this->settings['data']['literature'];
		$request = $this->client->get($literatureUrl);
		$response = $request->send();
		if ($response->getBody()) {
			return $response->getBody();
		}
		return '';
	}

	/**
	 * @param string $entityName
	 */
	public function entityAction($entityName) {

		$entityName = filter_var($entityName, FILTER_SANITIZE_STRING);

		$entityFile = FLOW_PATH_DATA . 'GermaniaSacra/Data/' . $entityName . '.json';
		$controller = ucfirst($entityName) . 'Controller';
		$controllerName = '\\Subugoe\\GermaniaSacra\\Controller\\' . $controller;

		if (!class_exists($controllerName)) {
			throw new Exception('Class ' . $controllerName . ' not found.', 1409817407);
		}

		/** @var \TYPO3\Flow\Mvc\Controller\ActionController $controllerInstance */
		$controllerInstance = new $controllerName();
		$controllerInstance->initializeAction();

		if (file_exists($entityFile)) {
			$content = file_get_contents($entityFile);
			$date = new \DateTime();
			$date->setTimestamp(filemtime($entityFile));
			$this->response->setLastModified(gmdate('D, d M Y H:i:s', filemtime($entityFile)) . '  GMT');
			$this->response->setHeader('Content-Type', 'application/json');
			return $content;
		} else {
			// TODO generate for all entities
			if ($entityName === 'kloster') {
				JsonGeneratorUtility::generateJsonFile($entityName);
			}
			$this->forward('list', $entityName, 'Subugoe.GermaniaSacra', array('format' => 'json'));
		}
	}
} 