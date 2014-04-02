<?php
namespace SUB\Germania\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use SUB\Germania\Domain\Model\Ort;

class OrtController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('orts', $this->ortRepository->findAll());
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Ort $ort
	 * @return void
	 */
	public function showAction(Ort $ort) {
		$this->view->assign('ort', $ort);
	}

	/**
	 * @return void
	 */
	public function newAction() {
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Ort $newOrt
	 * @return void
	 */
	public function createAction(Ort $newOrt) {
		$this->ortRepository->add($newOrt);
		$this->addFlashMessage('Created a new ort.');
		$this->redirect('index');
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Ort $ort
	 * @return void
	 */
	public function editAction(Ort $ort) {
		$this->view->assign('ort', $ort);
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Ort $ort
	 * @return void
	 */
	public function updateAction(Ort $ort) {
		$this->ortRepository->update($ort);
		$this->addFlashMessage('Updated the ort.');
		$this->redirect('index');
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Ort $ort
	 * @return void
	 */
	public function deleteAction(Ort $ort) {
		$this->ortRepository->remove($ort);
		$this->addFlashMessage('Deleted a ort.');
		$this->redirect('index');
	}

}

?>