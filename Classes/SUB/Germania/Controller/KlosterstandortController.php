<?php
namespace SUB\Germania\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use SUB\Germania\Domain\Model\Klosterstandort;

class KlosterstandortController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\KlosterstandortRepository
	 */
	protected $klosterstandortRepository;

	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;
	
	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;
	
	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('klosterstandorts', $this->klosterstandortRepository->findAll());
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Klosterstandort $klosterstandort
	 * @return void
	 */
	public function showAction(Klosterstandort $klosterstandort) {
		$this->view->assign('klosterstandort', $klosterstandort);
	}

	/**
	 * @return void
	 */
	public function newAction() {
		$this->view->assign('klosters', $this->klosterRepository->findAll());
		$this->view->assign('orts', $this->ortRepository->findAll());	
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Klosterstandort $newKlosterstandort
	 * @return void
	 */
	public function createAction(Klosterstandort $newKlosterstandort) {
		$this->klosterstandortRepository->add($newKlosterstandort);
		$this->addFlashMessage('Created a new klosterstandort.');
		$this->redirect('index');
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Klosterstandort $klosterstandort
	 * @return void
	 */
	public function editAction(Klosterstandort $klosterstandort) {
		$this->view->assign('klosterstandort', $klosterstandort);
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Klosterstandort $klosterstandort
	 * @return void
	 */
	public function updateAction(Klosterstandort $klosterstandort) {
		$this->klosterstandortRepository->update($klosterstandort);
		$this->addFlashMessage('Updated the klosterstandort.');
		$this->redirect('index');
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Klosterstandort $klosterstandort
	 * @return void
	 */
	public function deleteAction(Klosterstandort $klosterstandort) {
		$this->klosterstandortRepository->remove($klosterstandort);
		$this->addFlashMessage('Deleted a klosterstandort.');
		$this->redirect('index');
	}

}

?>