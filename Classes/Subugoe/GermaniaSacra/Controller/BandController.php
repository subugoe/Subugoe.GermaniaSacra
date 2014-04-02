<?php
namespace Subugoe\GermaniaSacra\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Band;

class BandController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacraDomain\Repository\BandRepository
	 */
	protected $bandRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('bands', $this->bandRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Band $band
	 * @return void
	 */
	public function showAction(Band $band) {
		$this->view->assign('band', $band);
	}

	/**
	 * @return void
	 */
	public function newAction() {
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Band $newBand
	 * @return void
	 */
	public function createAction(Band $newBand) {
		$this->bandRepository->add($newBand);
		$this->addFlashMessage('Created a new band.');
		$this->redirect('index');
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Band $band
	 * @return void
	 */
	public function editAction(Band $band) {
		$this->view->assign('band', $band);
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Band $band
	 * @return void
	 */
	public function updateAction(Band $band) {
		$this->bandRepository->update($band);
		$this->addFlashMessage('Updated the band.');
		$this->redirect('index');
	}

	/**
	 * @param \Subugoe\GermaniaSacraDomain\Model\Band $band
	 * @return void
	 */
	public function deleteAction(Band $band) {
		$this->bandRepository->remove($band);
		$this->addFlashMessage('Deleted a band.');
		$this->redirect('index');
	}

}

?>