<?php
namespace Subugoe\GermaniaSacra\Service;

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

/**
 * Generates json files from entities
 */
class JsonGeneratorService {

	/**
	 * @param $entityName
	 * @return bool
	 */
	public function generateJsonFile($entityName) {
		$entityName = filter_var($entityName, FILTER_SANITIZE_STRING);

		$entityControllerName = '\\Subugoe\\GermaniaSacra\\Controller\\' . ucfirst($entityName) . 'Controller';
		$entityFile = FLOW_PATH_DATA . 'GermaniaSacra/Data/' . $entityName . '.json';

		if (!is_dir(dirname($entityFile))) {
			mkdir(dirname($entityFile), 0777, TRUE);
		}
		try {
			/** @var \TYPO3\Flow\Mvc\Controller\ActionController $entityController */
			$entityController = new $entityControllerName();
			// TODO unified json generation for all models
			if ($entityName === 'kloster') {
				file_put_contents($entityFile, $entityController->allAsJson());
			} else {
				file_put_contents($entityFile, $this->getJsonFromHttpRequest($entityName));

				// generate master entity file when some entities change
				$this->generateJsonFile('kloster');
			}
			return TRUE;
		} catch (\Exception $e) {
			return FALSE;
		}
	}

	/**
	 * Retrieve a json file by using http requests
	 *
	 * @param string $entityName
	 * @return string
	 */
	protected function getJsonFromHttpRequest($entityName) {

		$browser = new \Guzzle\Http\Client();
		$request = $browser->createRequest('POST', 'http://germaniasacra.sub.uni-goettingen.de/subugoe.germaniasacra/' . $entityName . '/list.json');
		$request->setHeader('Accept', 'application/json');

		$body =  $request->send();
		return $body->getBody();
	}

} 