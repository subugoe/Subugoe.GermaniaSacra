<?php
namespace Subugoe\GermaniaSacra\Controller;

use Subugoe\GermaniaSacra\Domain\Model\Urltyp;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\RestController;

class UrltypController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\UrltypRepository
	 */
	protected $urltypRepository;

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
			$this->view->setVariablesToRender(array('urltyp'));
		}
		$this->view->assign('urltyp', $this->urltypRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Urltyp $urltyp
	 * @return void
	 */
	public function showAction(Urltyp $urltyp) {
		$this->view->setVariablesToRender(array('urltyp'));
		$this->view->assign('urltyp', $urltyp);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Urltyp $newUrltyp
	 * @return void
	 */
	public function createAction(Urltyp $newUrltyp) {
		$this->urltypRepository->add($newUrltyp);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Urltyp $urltyp
	 * @return void
	 */
	public function updateAction(Urltyp $urltyp) {
		$this->urltypRepository->update($urltyp);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Urltyp $urltyp
	 * @return void
	 */
	public function deleteAction(Urltyp $urltyp) {
		$this->urltypRepository->remove($urltyp);
	}

	/**
	 * Updates the list of Urltyp
	 * @return void
	 */
	public function listupdateAction() {
		$urltyps = $this->request->getArguments();
		foreach ($urltyps as $uuid => $urltyp) {
			$urltypObj = $this->urltypRepository->findByIdentifier($uuid);
			$urltypObj->setName($urltyp['name']);
			$this->urltypRepository->update($urltypObj);
		}

		$this->persistenceManager->persistAll();

		$this->throwStatus(200, NULL, Null);
	}
}
?>