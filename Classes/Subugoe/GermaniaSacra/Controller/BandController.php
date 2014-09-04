<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Band;
use TYPO3\Flow\Mvc\Controller\RestController;

class BandController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BandRepository
	 */
	protected $bandRepository;

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
			$this->view->setVariablesToRender(array('bands'));
		}
		$this->view->assign('bands', $this->bandRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Band $band
	 * @return void
	 */
	public function showAction(Band $band) {
		$this->view->setVariablesToRender(array('band'));
		$this->view->assign('band', $band);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Band $newBand
	 * @return void
	 */
	public function createAction(Band $newBand) {
		$this->bandRepository->add($newBand);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Band $band
	 * @return void
	 */
	public function updateAction(Band $band) {
		$this->bandRepository->update($band);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Band $band
	 * @return void
	 */
	public function deleteAction(Band $band) {
		$this->bandRepository->remove($band);
	}

	/**
	 * Updates the list of Band
	 * @return void
	 */
	public function listupdateAction() {
		$bands = $this->request->getArguments();
		foreach ($bands as $uuid => $band) {
			$bandObj = $this->bandRepository->findByIdentifier($uuid);
			$bandObj->setNummer($band['nummer']);
			$bandObj->setTitel($band['titel']);
			$bandObj->setKurztitel($band['kurztitel']);
			$bandObj->setSortierung($band['sortierung']);
			$this->bandRepository->update($bandObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>