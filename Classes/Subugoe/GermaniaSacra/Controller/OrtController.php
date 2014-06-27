<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Ort;
use TYPO3\Flow\Mvc\Controller\RestController;

class OrtController extends RestController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

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
			$this->view->setVariablesToRender(array('orts'));
		}
		$this->view->assign('orts', $this->ortRepository->findOrts());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function showAction(Ort $ort) {
		$this->view->setVariablesToRender(array('ort'));
		$this->view->assign('ort', $ort);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $newOrt
	 * @return void
	 */
	public function createAction(Ort $newOrt) {
		$this->ortRepository->add($newOrt);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function updateAction(Ort $ort) {
		$this->ortRepository->update($ort);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function deleteAction(Ort $ort) {
		$this->ortRepository->remove($ort);
	}

}

?>