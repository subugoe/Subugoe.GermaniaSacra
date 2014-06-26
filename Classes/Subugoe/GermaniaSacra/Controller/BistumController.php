<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\RestController;
use \Subugoe\GermaniaSacra\Domain\Model\Bistum;

class BistumController extends RestController {

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
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\Flow\\Mvc\\View\\JsonView';

	/**
	 * @return void
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('bistum'));
		}
		$this->view->assign('bistum', $this->bistumRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function showAction(Bistum $bistum) {
		$this->view->setVariablesToRender(array('bistum'));
		$this->view->assign('bistum', $bistum);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function createAction(Bistum $bistum) {
		$this->bistumRepository->add($bistum);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function updateAction(Bistum $bistum) {
		$this->bistumRepository->update($bistum);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function deleteAction(Bistum $bistum) {
		$this->bistumRepository->remove($bistum);
	}

}

?>