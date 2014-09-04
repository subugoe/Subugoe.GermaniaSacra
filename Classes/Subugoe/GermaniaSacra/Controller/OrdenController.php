<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Orden;
use TYPO3\Flow\Mvc\Controller\RestController;

class OrdenController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenRepository
	 */
	protected $ordenRepository;

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
	 * @return void
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('orden'));
		}

		$this->view->assign('orden', $this->ordenRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Orden $orden
	 * @return void
	 */
	public function showAction(Orden $orden) {
		$this->view->setVariablesToRender(array('orden'));
		$this->view->assign('orden', $orden);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Orden $newOrden
	 * @return void
	 */
	public function createAction(Orden $newOrden) {
		$this->ordenRepository->add($newOrden);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Orden $orden
	 * @return void
	 */
	public function updateAction(Orden $orden) {
		$this->ordenRepository->update($orden);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Orden $orden
	 * @return void
	 */
	public function deleteAction(Orden $orden) {
		$this->ordenRepository->remove($orden);
	}

	/**
	 * Updates the list of Orden
	 * @return void
	 */
	public function listupdateAction() {
		$ordens = $this->request->getArguments();
		foreach ($ordens as $uuid => $orden) {
			$ordenObj = $this->ordenRepository->findByIdentifier($uuid);
			$ordenObj->setOrden($orden['orden']);
			$ordenObj->setOrdo($orden['ordo']);
			$ordenObj->setSymbol($orden['symbol']);
			$ordenObj->setGraphik($orden['graphik']);
			$this->ordenRepository->update($ordenObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>