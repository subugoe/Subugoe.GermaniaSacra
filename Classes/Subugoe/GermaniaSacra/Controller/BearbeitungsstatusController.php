<?php
namespace Subugoe\GermaniaSacra\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus;

class BearbeitungsstatusController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacraDomain\Repository\BearbeitungsstatusRepository
	 */
	protected $bearbeitungsstatusRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('bearbeitungsstatuses', $this->bearbeitungsstatusRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function showAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->view->assign('bearbeitungsstatus', $bearbeitungsstatus);
	}

	/**
	 * @return void
	 */
	public function newAction() {
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Bearbeitungsstatus $newBearbeitungsstatus
	 * @return void
	 */
	public function createAction(Bearbeitungsstatus $newBearbeitungsstatus) {
		$this->bearbeitungsstatusRepository->add($newBearbeitungsstatus);
		$this->addFlashMessage('Created a new bearbeitungsstatus.');
		$this->redirect('index');
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function editAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->view->assign('bearbeitungsstatus', $bearbeitungsstatus);
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function updateAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->bearbeitungsstatusRepository->update($bearbeitungsstatus);
		$this->addFlashMessage('Updated the bearbeitungsstatus.');
		$this->redirect('index');
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function deleteAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->bearbeitungsstatusRepository->remove($bearbeitungsstatus);
		$this->addFlashMessage('Deleted a bearbeitungsstatus.');
		$this->redirect('index');
	}

}

?>