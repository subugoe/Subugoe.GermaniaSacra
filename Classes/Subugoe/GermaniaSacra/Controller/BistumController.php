<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use \Subugoe\GermaniaSacra\Domain\Model\Bistum;
use Subugoe\GermaniaSacra\Domain\Model\Url;
use Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl;

class BistumController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumRepository
	 */
	protected $bistumRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\UrlRepository
	 */
	protected $urlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\UrltypRepository
	 */
	protected $urltypRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumHasUrlRepository
	 */
	protected $bistumHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BandRepository
	 */
	protected $bandRepository;

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('text/html', 'application/json');

	/**
	 * @var array
	 */
	protected $viewFormatToObjectNameMap = array(
			'json' => 'TYPO3\\Flow\\Mvc\\View\\JsonView',
			'html' => 'TYPO3\\Fluid\\View\\TemplateView'
	);

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\Flow\\Mvc\\View\\JsonView';

	/**
	 * @return void
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('bistum'));
		}
		$this->view->assign('bistum', ['data' => $this->bistumRepository->findAll()]);
	}

	/**
	 * Create a new Bistum entity
	 * @return void
	 */
	public function createAction() {
		$bistumObj = new Bistum();
		if (is_object($bistumObj)) {
			$bistumObj->setBistum($this->request->getArgument('bistum'));
			$bistumObj->setKirchenprovinz($this->request->getArgument('kirchenprovinz'));
			$bistumObj->setBemerkung($this->request->getArgument('bemerkung'));
			$bistumObj->setIst_erzbistum($this->request->hasArgument('ist_erzbistum'));
			$bistumObj->setShapefile($this->request->getArgument('shapefile'));

			if ($this->request->hasArgument('ort')) {
				$ortUUID = $this->request->getArgument('ort');
				$ortObj = $this->ortRepository->findByIdentifier($ortUUID);
				if (is_object($ortObj)) {
					$bistumObj->setOrt($ortObj);
				}
			}
			$this->bistumRepository->add($bistumObj);
			// Add GND if set
			if ($this->request->hasArgument('gnd')) {
				$gnd = $this->request->getArgument('gnd');
				if ($this->request->hasArgument('gnd_label')) {
					$gnd_label = $this->request->getArgument('gnd_label');
				}
				if (empty($gnd_label)) {
					$gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
					$gnd_label = $this->request->getArgument('bistum') . ' [' . $gndid . ']';
				}
				if (isset($gnd) && !empty($gnd)) {
						$url = new Url();
						$url->setUrl($gnd);
						if (!empty($gnd_label)) {
							$url->setBemerkung($gnd_label);
						}
						$urlTypObj = $this->urltypRepository->findOneByName('GND');
						$url->setUrltyp($urlTypObj);
						$this->urlRepository->add($url);
						$urlUUID = $url->getUUID();
						$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
						$bistumhasurl = new BistumHasUrl();
						$bistumhasurl->setBistum($bistumObj);
						$bistumhasurl->setUrl($urlObj);
						$this->bistumHasUrlRepository->add($bistumhasurl);
					}
			}
			//Update Wikipedia if set
			$bistumHasWiki = false;
			if ($this->request->hasArgument('wikipedia')) {
				$wikipedia = $this->request->getArgument('wikipedia');
				if ($this->request->hasArgument('wikipedia_label')) {
					$wikipedia_label = $this->request->getArgument('wikipedia_label');
				}
				if (empty($wikipedia_label)) {
					$wikipedia_label = str_replace('http://de.wikipedia.org/wiki/', '', trim($wikipedia));
					$wikipedia_label = str_replace('_', ' ', $wikipedia_label);
					$wikipedia_label = rawurldecode($wikipedia_label);
				}
				if (isset($wikipedia) && !empty($wikipedia)) {
						$url = new Url();
						$url->setUrl($wikipedia);
						if (!empty($wikipedia_label)) {
							$url->setBemerkung($wikipedia_label);
						}
						$urlTypObj = $this->urltypRepository->findOneByName('Wikipedia');
						$url->setUrltyp($urlTypObj);
						$this->urlRepository->add($url);
						$urlUUID = $url->getUUID();
						$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
						$bistumhasurl = new BistumHasUrl();
						$bistumhasurl->setBistum($bistumObj);
						$bistumhasurl->setUrl($urlObj);
						$this->bistumHasUrlRepository->add($bistumhasurl);
					}
			}
			// Add Url if set
			if ($this->request->hasArgument('url')) {
				$urlArr = $this->request->getArgument('url');
				if (isset($urlArr) && !empty($urlArr)) {
					if ($this->request->hasArgument('url_typ')) {
						$urlTypArr = $this->request->getArgument('url_typ');
					}
					if ($this->request->hasArgument('links_label')) {
						$linksLabelArr = $this->request->getArgument('links_label');
					}
					if ((isset($urlArr) && !empty($urlArr)) && (isset($urlTypArr) && !empty($urlTypArr))) {
						foreach ($urlArr as $k => $url) {
							if (!empty($url)) {
								$urlObj = new Url();
								$urlObj->setUrl($url);
								$urlTypObj = $this->urltypRepository->findByIdentifier($urlTypArr[$k]);
								$urlTyp = $urlTypObj->getName();
								$urlObj->setUrltyp($urlTypObj);
								if (isset($linksLabelArr[$k]) && !empty($linksLabelArr[$k])) {
									$urlObj->setBemerkung($linksLabelArr[$k]);
								}
								else {
									$urlObj->setBemerkung($urlTyp);
								}
								$this->urlRepository->add($urlObj);
								$bistumhasurlObj = new BistumHasUrl();
								$bistumhasurlObj->setBistum($bistumObj);
								$bistumhasurlObj->setUrl($urlObj);
								$this->bistumHasUrlRepository->add($bistumhasurlObj);
							}
						}
					}
				}
			}
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Entity Bistum not available', Null);
		}
	}

	/**
	 * Edit a Bistum entity
	 * @return array $bistumArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$bistumArr = array();
		$bistumObj = $this->bistumRepository->findByIdentifier($uuid);
		$bistumArr['uUID'] = $bistumObj->getUUID();
		$bistumArr['bistum'] = $bistumObj->getBistum();
		$bistumArr['kirchenprovinz'] = $bistumObj->getKirchenprovinz();
		$bistumArr['bemerkung'] = $bistumObj->getBemerkung();
		$bistumArr['ist_erzbistum'] = $bistumObj->getIst_erzbistum();
		$bistumArr['shapefile'] = $bistumObj->getShapefile();
		$ort = $bistumObj->getOrt();
		if ( $ort )
			$bistumArr['ort'] = array('uuid' => $ort->getUUID(), 'name' => $ort->getOrt());
		else
			$bistumArr['ort'] = array();
		// Bistum Url data
		$Urls = array();
		$bistumHasUrls = $bistumObj->getBistumHasUrls();
		foreach ($bistumHasUrls as $k => $bistumHasUrl) {
			$urlObj = $bistumHasUrl->getUrl();
			$url = rawurldecode($urlObj->getUrl());
			$url_bemerkung = $urlObj->getBemerkung();
			if ($url !== 'keine Angabe') {
				$urlTypObj = $urlObj->getUrltyp();
				if (is_object($urlTypObj)) {
					$urlTyp = $urlTypObj->getUUID();
					$urlTypName = $urlTypObj->getName();
					if ($urlTypName == 'GND' || $urlTypName == 'Wikipedia') {
						$Urls[$k] = array('url_typ' => $urlTyp, 'url' => $url, 'url_label' => $url_bemerkung, 'url_typ_name' => $urlTypName);
					}
					else {
						$Urls[$k] = array('url_typ' => $urlTyp, 'url' => $url, 'links_label' => $url_bemerkung, 'url_typ_name' => $urlTypName);
					}
				}
			}
		}
		$bistumArr['url'] = $Urls;

		return json_encode($bistumArr);
	}

	/**
	 * Update a Bistum entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$bistumObj = $this->bistumRepository->findByIdentifier($uuid);
		if (is_object($bistumObj)) {
			$bistumObj->setBistum($this->request->getArgument('bistum'));
			$bistumObj->setKirchenprovinz($this->request->getArgument('kirchenprovinz'));
			$bistumObj->setBemerkung($this->request->getArgument('bemerkung'));
			$bistumObj->setIst_erzbistum($this->request->hasArgument('ist_erzbistum'));
			$bistumObj->setShapefile($this->request->getArgument('shapefile'));
			if ($this->request->hasArgument('ort')) {
				$ortUUID = $this->request->getArgument('ort');
				$ortObj = $this->ortRepository->findByIdentifier($ortUUID);
				if (is_object($ortObj)) {
					$bistumObj->setOrt($ortObj);
				}
			}
			$this->bistumRepository->update($bistumObj);
			// Fetch Bistum Urls
			$bistumHasUrls = $bistumObj->getBistumHasUrls();
			$bistumHasGND = false;
			// Update GND if set
			if ($this->request->hasArgument('gnd')) {
				$gnd = $this->request->getArgument('gnd');
				if ($this->request->hasArgument('gnd_label')) {
					$gnd_label = $this->request->getArgument('gnd_label');
				}
				if (empty($gnd_label)) {
					$gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
					$gnd_label = $this->request->getArgument('bistum') . ' [' . $gndid . ']';
				}
				if (isset($gnd) && !empty($gnd)) {
					if (!empty($bistumHasUrls)) {
						foreach ($bistumHasUrls as $i => $bistumHasUrl) {
							$urlObj = $bistumHasUrl->getUrl();
							$urlTypObj = $urlObj->getUrltyp();
							$urlTyp = $urlTypObj->getName();
							if ($urlTyp == "GND") {
								$urlObj->setUrl($gnd);
								if (!empty($gnd_label)) {
									$urlObj->setBemerkung($gnd_label);
								}
								$this->urlRepository->update($urlObj);
								$bistumHasGND = true;
							}
						}
					}
					if (!$bistumHasGND) {
						$url = new Url();
						$url->setUrl($gnd);
						if (!empty($gnd_label)) {
							$url->setBemerkung($gnd_label);
						}
						$urlTypObj = $this->urltypRepository->findOneByName('GND');
						$url->setUrltyp($urlTypObj);
						$this->urlRepository->add($url);
						$urlUUID = $url->getUUID();
						$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
						$bistumhasurl = new BistumHasUrl();
						$bistumhasurl->setBistum($bistumObj);
						$bistumhasurl->setUrl($urlObj);
						$this->bistumHasUrlRepository->add($bistumhasurl);
					}
				}
			}
			//Update Wikipedia if set
			$bistumHasWiki = false;
			if ($this->request->hasArgument('wikipedia')) {
				$wikipedia = $this->request->getArgument('wikipedia');
				if ($this->request->hasArgument('wikipedia_label')) {
					$wikipedia_label = $this->request->getArgument('wikipedia_label');
				}
				if (empty($wikipedia_label)) {
					$wikipedia_label = str_replace('http://de.wikipedia.org/wiki/', '', trim($wikipedia));
					$wikipedia_label = str_replace('_', ' ', $wikipedia_label);
					$wikipedia_label = rawurldecode($wikipedia_label);
				}
				if (isset($wikipedia) && !empty($wikipedia)) {
					foreach ($bistumHasUrls as $i => $bistumHasUrl) {
						$urlObj = $bistumHasUrl->getUrl();
						$urlTypObj = $urlObj->getUrltyp();
						$urlTyp = $urlTypObj->getName();
						if ($urlTyp == "Wikipedia") {
							$urlObj->setUrl($wikipedia);
							if (!empty($wikipedia_label)) {
								$urlObj->setBemerkung($wikipedia_label);
							}
							$this->urlRepository->update($urlObj);
							$bistumHasWiki = true;
						}
					}
					if (!$bistumHasWiki) {
						$url = new Url();
						$url->setUrl($wikipedia);
						if (!empty($wikipedia_label)) {
							$url->setBemerkung($wikipedia_label);
						}
						$urlTypObj = $this->urltypRepository->findOneByName('Wikipedia');
						$url->setUrltyp($urlTypObj);
						$this->urlRepository->add($url);
						$urlUUID = $url->getUUID();
						$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
						$bistumhasurl = new BistumHasUrl();
						$bistumhasurl->setBistum($bistumObj);
						$bistumhasurl->setUrl($urlObj);
						$this->bistumHasUrlRepository->add($bistumhasurl);
					}
				}
			}
			// Add Url if set
			if ($this->request->hasArgument('url')) {
				$urlArr = $this->request->getArgument('url');
				if (isset($urlArr) && !empty($urlArr)) {
					if ($this->request->hasArgument('url_typ')) {
						$urlTypArr = $this->request->getArgument('url_typ');
					}
					if ($this->request->hasArgument('links_label')) {
						$linksLabelArr = $this->request->getArgument('links_label');
					}
					if ((isset($urlArr) && !empty($urlArr)) && (isset($urlTypArr) && !empty($urlTypArr))) {
						foreach ($bistumHasUrls as $i => $bistumHasUrl) {
							$urlObj = $bistumHasUrl->getUrl();
							$urlTypObj = $urlObj->getUrltyp();
							$urlTyp = $urlTypObj->getName();
							if ($urlTyp != "Wikipedia" && $urlTyp != "GND") {
								$this->bistumHasUrlRepository->remove($bistumHasUrl);
								$this->urlRepository->remove($urlObj);
							}
						}
						foreach ($urlArr as $k => $url) {
							if (!empty($url)) {
								$urlObj = new Url();
								$urlObj->setUrl($url);
								$urlTypObj = $this->urltypRepository->findByIdentifier($urlTypArr[$k]);
								$urlTyp = $urlTypObj->getName();
								$urlObj->setUrltyp($urlTypObj);
								if (isset($linksLabelArr[$k]) && !empty($linksLabelArr[$k])) {
									$urlObj->setBemerkung($linksLabelArr[$k]);
								}
								else {
									$urlObj->setBemerkung($urlTyp);
								}
								$this->urlRepository->add($urlObj);
								$bistumhasurlObj = new BistumHasUrl();
								$bistumhasurlObj->setBistum($bistumObj);
								$bistumhasurlObj->setUrl($urlObj);
								$this->bistumHasUrlRepository->add($bistumhasurlObj);
							}
						}
					}
				}
			}
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Entity Bistum not available', Null);
		}
	}

	/**
	 * Delete an Bistum entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$orte = count($this->ortRepository->findByBistum($uuid));
		$bistumhasurls = count($this->bistumHasUrlRepository->findByBistum($uuid));
		$bands = count($this->bandRepository->findByBistum($uuid));
		if ($orte == 0 && $bistumhasurls == 0 && $bands == 0) {
			$bistumObj = $this->bistumRepository->findByIdentifier($uuid);
			if (!is_object($bistumObj)) {
				$this->throwStatus(400, 'Entity Bistum not available', Null);
			}
			$this->bistumRepository->remove($bistumObj);
			// Fetch Bistum Urls
			$bistumHasUrls = $bistumObj->getBistumHasUrls();
			if (is_array($bistumHasUrls)) {
				foreach ($bistumHasUrls as $bistumHasUrl) {
					$this->bistumHasUrlRepository->remove($bistumHasUrl);
				}
			}
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Due to dependencies Bistum entity could not be deleted', Null);
		}
	}

	/**
	 * Update a list of Bistum entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$bistumlist = $this->request->getArgument('data');
		}
		if (empty($bistumlist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', Null);
		}
		foreach ($bistumlist as $uuid => $bistum) {
			$bistumObj = $this->bistumRepository->findByIdentifier($uuid);
			$bistumObj->setBistum($bistum['bistum']);
			$bistumObj->setKirchenprovinz($bistum['kirchenprovinz']);
			$bistumObj->setBemerkung($bistum['bemerkung']);
			if (isset($bistum['ist_erzbistum']) && !empty($bistum['ist_erzbistum'])) {
				$ist_erzbistum = $bistum['ist_erzbistum'];
			}
			else {
				$ist_erzbistum = 0;
			}
			$bistumObj->setIst_erzbistum($ist_erzbistum);
			$bistumObj->setShapefile($bistum['shapefile']);
			$this->bistumRepository->update($bistumObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, Null);
	}
}
?>