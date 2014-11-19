<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Ort;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Url;
use Subugoe\GermaniaSacra\Domain\Model\OrtHasUrl;

class OrtController extends AbstractBaseController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtHasUrlRepository
	 */
	protected $ortHasUrlRepository;

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LandRepository
	 */
	protected $landRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumRepository
	 */
	protected $bistumRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterstandortRepository
	 */
	protected $klosterstandortRepository;

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
	 * @return void
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('ort'));
		}
		$this->view->assign('ort', ['data' => $this->ortRepository->findAll()]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
	}

	/**
	 * Create a new Ort entity
	 * @return void
	 */
	public function createAction() {
		$ortObj = new Ort();
		if (is_object($ortObj)) {
			$ortObj->setOrt($this->request->getArgument('ort'));
			$ortObj->setGemeinde($this->request->getArgument('gemeinde'));
			$ortObj->setKreis($this->request->getArgument('kreis'));
			$ortObj->setWuestung($this->request->hasArgument('wuestung'));
			$ortObj->setBreite($this->request->getArgument('breite'));
			$ortObj->setLaenge($this->request->getArgument('laenge'));
			if ($this->request->hasArgument('bistum')) {
				$bistumUUID = $this->request->getArgument('bistum');
				$bistumObj = $this->bistumRepository->findByIdentifier($bistumUUID);
				if (is_object($bistumObj)) {
					$ortObj->setBistum($bistumObj);
				}
			}
			if ($this->request->hasArgument('land')) {
				$landUUID = $this->request->getArgument('land');
				$landObj = $this->landRepository->findByIdentifier($landUUID);
				if (is_object($landObj)) {
					$ortObj->setLand($landObj);
				}
			}
			$this->ortRepository->add($ortObj);
			// Add GND if set
			if ($this->request->hasArgument('gnd')) {
				$gnd = $this->request->getArgument('gnd');
				if ($this->request->hasArgument('gnd_label')) {
					$gnd_label = $this->request->getArgument('gnd_label');
				}
				if (empty($gnd_label)) {
					$gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
					$gnd_label = $this->request->getArgument('ort') . ' [' . $gndid . ']';
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
					$orthasurl = new OrtHasUrl();
					$orthasurl->setOrt($ortObj);
					$orthasurl->setUrl($urlObj);
					$this->ortHasUrlRepository->add($orthasurl);
				}
			}
			//Add Wikipedia if set
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
					$orthasurl = new OrtHasUrl();
					$orthasurl->setOrt($ortObj);
					$orthasurl->setUrl($urlObj);
					$this->ortHasUrlRepository->add($orthasurl);
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
								$orthasurl = new OrtHasUrl();
								$orthasurl->setOrt($ortObj);
								$orthasurl->setUrl($urlObj);
								$this->ortHasUrlRepository->add($orthasurl);
							}
						}
					}
				}
			}
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, NULL);
		}
		else {
			$this->throwStatus(400, 'Entity Ort not available', NULL);
		}
	}

	/**
	 * Edit a Ort entity
	 * @return array $ortArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$ortArr = array();
		$ort = $this->ortRepository->findByIdentifier($uuid);
		$ortArr['uUID'] = $ort->getUUID();
		$ortArr['ort'] = $ort->getOrt();
		$ortArr['gemeinde'] = $ort->getGemeinde();
		$ortArr['kreis'] = $ort->getKreis();
		$ortArr['wuestung'] = $ort->getWuestung();
		$ortArr['breite'] = $ort->getBreite();
		$ortArr['laenge'] = $ort->getLaenge();
		$land = $ort->getLand();
		$ortArr['land'] = is_object($land) ? $land->getUUID() : NULL;
		$bistum = $ort->getBistum();
		$ortArr['bistum'] = is_object($bistum) ? $bistum->getUUID() : NULL;
		// Ort Url data
		$Urls = array();
		$ortHasUrls = $ort->getOrtHasUrls();
		foreach ($ortHasUrls as $k => $ortHasUrl) {
			$urlObj = $ortHasUrl->getUrl();
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
		$ortArr['url'] = $Urls;

		return json_encode($ortArr);
	}

	/**
	 * Update a Ort entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$ortObj = $this->ortRepository->findByIdentifier($uuid);
		if (is_object($ortObj)) {
			$ortObj->setOrt($this->request->getArgument('ort'));
			$ortObj->setGemeinde($this->request->getArgument('gemeinde'));
			$ortObj->setKreis($this->request->getArgument('kreis'));
			$ortObj->setWuestung($this->request->hasArgument('wuestung'));
			$ortObj->setBreite($this->request->getArgument('breite'));
			$ortObj->setLaenge($this->request->getArgument('laenge'));
			if ($this->request->hasArgument('bistum')) {
				$bistumUUID = $this->request->getArgument('bistum');
				$bistumObj = $this->bistumRepository->findByIdentifier($bistumUUID);
				if (is_object($bistumObj)) {
					$ortObj->setBistum($bistumObj);
				}
			}
			if ($this->request->hasArgument('land')) {
				$landUUID = $this->request->getArgument('land');
				$landObj = $this->landRepository->findByIdentifier($landUUID);
				if (is_object($landObj)) {
					$ortObj->setLand($landObj);
				}
			}
			$this->ortRepository->update($ortObj);
			// Fetch Ort Urls
			$ortHasUrls = $ortObj->getOrtHasUrls();
			$ortHasGND = false;
			// Update GND if set
			if ($this->request->hasArgument('gnd')) {
				$gnd = $this->request->getArgument('gnd');
				if ($this->request->hasArgument('gnd_label')) {
					$gnd_label = $this->request->getArgument('gnd_label');
				}
				if (empty($gnd_label)) {
					$gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
					$gnd_label = $this->request->getArgument('ort') . ' [' . $gndid . ']';
				}
				if (isset($gnd) && !empty($gnd)) {
					if (!empty($ortHasUrls)) {
						foreach ($ortHasUrls as $i => $ortHasUrl) {
							$urlObj = $ortHasUrl->getUrl();
							$urlTypObj = $urlObj->getUrltyp();
							$urlTyp = $urlTypObj->getName();
							if ($urlTyp == "GND") {
								$urlObj->setUrl($gnd);
								if (!empty($gnd_label)) {
									$urlObj->setBemerkung($gnd_label);
								}
								$this->urlRepository->update($urlObj);
								$ortHasGND = true;
							}
						}
					}
					if (!$ortHasGND) {
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
						$orthasurl = new OrtHasUrl();
						$orthasurl->setOrt($ortObj);
						$orthasurl->setUrl($urlObj);
						$this->ortHasUrlRepository->add($orthasurl);
					}
				}
			}
			//Update Wikipedia if set
			$ortHasWiki = false;
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
					foreach ($ortHasUrls as $i => $ortHasUrl) {
						$urlObj = $ortHasUrl->getUrl();
						$urlTypObj = $urlObj->getUrltyp();
						$urlTyp = $urlTypObj->getName();
						if ($urlTyp == "Wikipedia") {
							$urlObj->setUrl($wikipedia);
							if (!empty($wikipedia_label)) {
								$urlObj->setBemerkung($wikipedia_label);
							}
							$this->urlRepository->update($urlObj);
							$ortHasWiki = true;
						}
					}
					if (!$ortHasWiki) {
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
						$orthasurl = new OrtHasUrl();
						$orthasurl->setOrt($ortObj);
						$orthasurl->setUrl($urlObj);
						$this->ortHasUrlRepository->add($orthasurl);
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
						foreach ($ortHasUrls as $i => $ortHasUrl) {
							$urlObj = $ortHasUrl->getUrl();
							$urlTypObj = $urlObj->getUrltyp();
							$urlTyp = $urlTypObj->getName();
							if ($urlTyp != "Wikipedia" && $urlTyp != "GND") {
								$this->ortHasUrlRepository->remove($ortHasUrl);
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
								$orthasurl = new OrtHasUrl();
								$orthasurl->setOrt($ortObj);
								$orthasurl->setUrl($urlObj);
								$this->ortHasUrlRepository->add($orthasurl);
							}
						}
					}
				}
			}
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, NULL);
		}
		else {
			$this->throwStatus(400, 'Entity Ort not available', NULL);
		}
	}

	/**
	 * Delete an Ort entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$klosterstandorte = count($this->klosterstandortRepository->findByOrt($uuid));
		$orthasurls = count($this->ortHasUrlRepository->findByOrt($uuid));
		$bistums = count($this->bistumRepository->findByOrt($uuid));
		if ($klosterstandorte == 0 && $orthasurls == 0 && $bistums == 0) {
			$ortObj = $this->ortRepository->findByIdentifier($uuid);
			if (!is_object($ortObj)) {
				$this->throwStatus(400, 'Entity Ort not available', NULL);
			}
			$this->ortRepository->remove($ortObj);
			// Fetch Ort Urls
			$ortHasUrls = $ortObj->getOrtHasUrls();
			if (is_array($ortHasUrls)) {
				foreach ($ortHasUrls as $ortHasUrl) {
					$this->ortHasUrlRepository->remove($ortHasUrl);
				}
			}
			$this->throwStatus(200, NULL, NULL);
		}
		else {
			$this->throwStatus(400, 'Due to dependencies Ort entity could not be deleted', NULL);
		}
	}

	/**
	 * Update a list of Ort entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$ortlist = $this->request->getArgument('data');
		}
		if (empty($ortlist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', NULL);
		}
		foreach ($ortlist as $uuid => $ort) {
			$ortObj = $this->ortRepository->findByIdentifier($uuid);
			$ortObj->setOrt($ort['ort']);
			$ortObj->setGemeinde($ort['gemeinde']);
			$ortObj->setKreis($ort['kreis']);
			$ortObj->setBreite($ort['breite']);
			$ortObj->setLaenge($ort['laenge']);
			if (isset($ort['wuestung']) && !empty($ort['wuestung'])) {
				$wuestung = $ort['wuestung'];
			}
			else {
				$wuestung = 0;
			}
			$ortObj->setWuestung($wuestung);
			$this->ortRepository->update($ortObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, NULL);
	}
}
?>
