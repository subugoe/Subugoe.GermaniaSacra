<?php
namespace Subugoe\GermaniaSacra\Controller;

use Subugoe\GermaniaSacra\Domain\Model\Land;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Mvc\Controller\RestController;

class LandController extends ActionController {

	/**
	* @Flow\Inject
	* @var \Subugoe\GermaniaSacra\Domain\Repository\LandRepository
	*/
	protected $landRepository;

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
			$this->view->setVariablesToRender(array('land'));
		}
		$this->view->assign('land', ['data' => $this->landRepository->findAll()]);
	}

	/**
	* @param \Subugoe\GermaniaSacra\Domain\Model\Land $land
	* @return void
	*/
	public function showAction(Land $land) {
		$this->view->setVariablesToRender(array('land'));
		$this->view->assign('land', $land);
	}

	/**
	* @param \Subugoe\GermaniaSacra\Domain\Model\Land $newBand
	* @return void
	*/
	public function createAction(Land $newBand) {
		$this->landRepository->add($newBand);
		$this->response->setStatus(201);
	}

	/**
	* @param \Subugoe\GermaniaSacra\Domain\Model\Land $land
	* @return void
	*/
	public function updateAction(Land $land) {
		$this->landRepository->update($land);
	}

	/**
	* @param \Subugoe\GermaniaSacra\Domain\Model\Land $land
	* @return void
	*/
	public function deleteAction(Land $land) {
		$this->landRepository->remove($land);
	}

	/**
	* Updates the list of Land
	* @return void
	*/
	public function listupdateAction() {
		$lands = $this->request->getArguments();
		foreach ($lands as $uuid => $land) {
			$landObj = $this->landRepository->findByIdentifier($uuid);
			$landObj->setLand($land['land']);
			$landObj->setIst_in_deutschland($land['ist_in_deutschland']);
			$this->landRepository->update($landObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>