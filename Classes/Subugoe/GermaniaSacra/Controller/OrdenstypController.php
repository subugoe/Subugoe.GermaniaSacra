<?php
namespace Subugoe\GermaniaSacra\Controller;

use Subugoe\GermaniaSacra\Domain\Model\Ordenstyp;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\RestController;

class OrdenstypController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenstypRepository
	 */
	protected $ordenstypRepository;

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
			$this->view->setVariablesToRender(array('ordenstyp'));
		}
		$this->view->assign('ordenstyp', $this->ordenstypRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp $ordenstyp
	 * @return void
	 */
	public function showAction(Ordenstyp $ordenstyp) {
		$this->view->setVariablesToRender(array('ordenstyp'));
		$this->view->assign('ordenstyp', $ordenstyp);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp $newOrdenstyp
	 * @return void
	 */
	public function createAction(Ordenstyp $newOrdenstyp) {
		$this->ordenstypRepository->add($newOrdenstyp);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp $ordenstyp
	 * @return void
	 */
	public function updateAction(Ordenstyp $ordenstyp) {
		$this->ordenstypRepository->update($ordenstyp);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp $ordenstyp
	 * @return void
	 */
	public function deleteAction(Ordenstyp $ordenstyp) {
		$this->ordenstypRepository->remove($ordenstyp);
	}

	/**
	 * Updates the list of Ordenstyp
	 * @return void
	 */
	public function listupdateAction() {
		$ordenstyps = $this->request->getArguments();
		foreach ($ordenstyps as $uuid => $ordenstyp) {
			$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
			$ordenstypObj->setOrdenstyp($ordenstyp['ordenstyp']);
			$this->ordenstypRepository->update($ordenstypObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>