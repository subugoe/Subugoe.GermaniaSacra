<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\RestController;
use \Subugoe\GermaniaSacra\Domain\Model\Bistum;

class BistumController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumRepository
	 */
	protected $bistumRepository;

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
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function showAction(Bistum $bistum) {
		$this->view->setVariablesToRender(array('bistum'));
		$this->view->assign('bistum', $bistum);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function createAction(Bistum $bistum) {
		$this->bistumRepository->add($bistum);
		$this->response->setStatus(201);
	}

	/**
	 * @return void
	 */
	public function editAction() {
		$uuid = $this->request->getArgument('uuid');
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
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function updateAction(Bistum $bistum) {
		$this->bistumRepository->update($bistum);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function deleteAction(Bistum $bistum) {
		$this->bistumRepository->remove($bistum);
	}

	/**
	 * Updates the list of Bistum
	 * @return void
	 */
	public function updateListAction() {
		$bistums = $this->request->getArgument('data');
		foreach ($bistums as $uuid => $bistum) {
			$bistumObj = $this->bistumRepository->findByIdentifier($uuid);
			$bistumObj->setBistum($bistum['bistum']);
			$bistumObj->setKirchenprovinz($bistum['kirchenprovinz']);
			$bistumObj->setBemerkung($bistum['bemerkung']);
			$bistumObj->setIst_erzbistum($bistum['ist_erzbistum']);
			$bistumObj->setShapefile($bistum['shapefile']);
			$this->bistumRepository->update($bistumObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>