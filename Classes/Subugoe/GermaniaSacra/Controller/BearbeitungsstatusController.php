<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus;
use TYPO3\Flow\Mvc\Controller\RestController;

class BearbeitungsstatusController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeitungsstatusRepository
	 */
	protected $bearbeitungsstatusRepository;

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
			$this->view->setVariablesToRender(array('bearbeitungsstatuses'));
		}
		$this->view->assign('bearbeitungsstatuses', $this->bearbeitungsstatusRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function showAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->view->setVariablesToRender(array('bearbeitungsstatus'));
		$this->view->assign('bearbeitungsstatus', $bearbeitungsstatus);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $newBearbeitungsstatus
	 * @return void
	 */
	public function createAction(Bearbeitungsstatus $newBearbeitungsstatus) {
		$this->bearbeitungsstatusRepository->add($newBearbeitungsstatus);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function updateAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->bearbeitungsstatusRepository->update($bearbeitungsstatus);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function deleteAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->bearbeitungsstatusRepository->remove($bearbeitungsstatus);
	}

	/**
	 * Updates the list of Bearbeitungsstatus
	 * @return void
	 */
	public function listupdateAction() {
		$bearbeitungsstatuses = $this->request->getArguments();
		foreach ($bearbeitungsstatuses as $uuid => $bearbeitungsstatus) {
			$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
			$bearbeitungsstatusObj->setName($bearbeitungsstatus['name']);
			$this->bearbeitungsstatusRepository->update($bearbeitungsstatusObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>