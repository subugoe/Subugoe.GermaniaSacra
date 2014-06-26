<?php
namespace Subugoe\GermaniaSacra\Controller;

use Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\RestController;

class PersonallistenstatusController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\PersonallistenstatusRepository
	 */
	protected $personallistenstatusRepository;

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
			$this->view->setVariablesToRender(array('personallistenstatus'));
		}
		$this->view->assign('personallistenstatus', $this->personallistenstatusRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $personallistenstatus
	 * @return void
	 */
	public function showAction(Personallistenstatus $personallistenstatus) {
		$this->view->setVariablesToRender(array('personallistenstatus'));
		$this->view->assign('personallistenstatus', $personallistenstatus);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $newPersonallistenstatus
	 * @return void
	 */
	public function createAction(Personallistenstatus $newPersonallistenstatus) {
		$this->personallistenstatusRepository->add($newPersonallistenstatus);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $personallistenstatus
	 * @return void
	 */
	public function updateAction(Personallistenstatus $personallistenstatus) {
		$this->personallistenstatusRepository->update($personallistenstatus);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $personallistenstatus
	 * @return void
	 */
	public function deleteAction(Personallistenstatus $personallistenstatus) {
		$this->personallistenstatusRepository->remove($personallistenstatus);
	}
}

?>