<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Ort;
use TYPO3\Flow\Mvc\Controller\RestController;

class OrtController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortHasUrlRepository;

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
	* The default argument name.
	* @var string
	*/
	protected $resourceArgumentName = 'ort';

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
	// TODO: Do we really need to load all Orts with the page?
	// Maybe this could be used only by the ProxyController while the template is rendered without loading anything from the DB
	public function listAction() {

		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('orts'));
		}
	
		$ortArr = array();
		$orts = $this->ortRepository->findOrts();

		foreach ($orts as $k => $ort) {

			$ortArr[$k]['uUID'] = $ort->getUUID();
			$ortArr[$k]['ort'] = $ort->getOrt();
			$ortArr[$k]['gemeinde'] = $ort->getGemeinde();
			$ortArr[$k]['kreis'] = $ort->getKreis();
			$ortArr[$k]['wuestung'] = $ort->getWuestung();
			$ortArr[$k]['breite'] = $ort->getBreite();
			$ortArr[$k]['laenge'] = $ort->getLaenge();
			$land = $ort->getLand();
			$ortArr[$k]['land'] = is_object($land) ? $land->getUUID() : null;
			$bistum = $ort->getBistum();
			$ortArr[$k]['bistum'] = is_object($bistum) ? $bistum->getUUID() : null;

			$ortHasUrls = $ort->getOrtHasUrls();
			$urlArr = array();
			foreach ($ortHasUrls as $ortHasUrl) {
				$urlObj = $ortHasUrl->getUrl();
				$url = $urlObj->getUrl();
				if (!empty($url)) {
					$urlTypObj = $urlObj->getUrltyp();
					$urlArr[$urlObj->getUUID()] = [
						'url' => $url,
						'url_typ' =>  $urlTypObj->getUUID(),
						'links_label' => $urlObj->getBemerkung()
					];
				}
			}
			$ortArr[$k]['urls'] = $urlArr;

		}
	
		$this->view->assign('orts', $ortArr);

	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function showAction(Ort $ort) {
		$this->view->setVariablesToRender(array('ort'));
		$this->view->assign('ort', $ort);
	}

	/**
	* Shows a form for creating a new ort object
	*/
	public function newAction() {
		$this->landRepository->setDefaultOrderings(
				array('land' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$this->bistumRepository->setDefaultOrderings(
				array('bistum' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('lands'));
			$this->view->setVariablesToRender(array('bistums'));
		}
		$this->view->assign('lands', $this->landRepository->findAll());
		$this->view->assign('bistums', $this->bistumRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $newOrt
	 * @return void
	 */
	public function createAction(Ort $newOrt) {
		$this->ortRepository->add($newOrt);
		// TODO: Get the uUIDs of newly created Orts
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function updateAction(Ort $ort) {
		$this->ortRepository->update($ort);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function deleteAction(Ort $ort) {
		$this->ortRepository->remove($ort);
	}

	/**
	 * Updates the list of Ort
	 * @return void
	 */
	public function listupdateAction() {
		$orts = $this->request->getArguments();
		foreach ($orts as $uuid => $ort) {
			$ortObj = $this->ortRepository->findByIdentifier($uuid);
			$ortObj->setOrt($ort['ort']);
			$ortObj->setGemeinde($ort['gemeinde']);
			$ortObj->setKreis($ort['kreis']);
			$ortObj->setBreite($ort['breite']);
			$ortObj->setLaenge($ort['laenge']);
			$ortObj->setWuestung($ort['wuestung']);
			$this->ortRepository->update($ortObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}

?>