<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Literatur;

/**
 * Class LiteraturController
 * @package Subugoe\GermaniaSacra\Controller
 * @deprecated
 */
class LiteraturController extends AbstractBaseController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LiteraturRepository
	 */
	protected $literaturRepository;

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
			$this->view->setVariablesToRender(array('literatur'));
		}
		$this->view->assign('literatur', ['data' => $this->literaturRepository->findAll()]);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Literatur $literatur
	 * @return void
	 */
	public function showAction(Literatur $literatur) {
		$this->view->setVariablesToRender(array('literatur'));
		$this->view->assign('literatur', $literatur);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Literatur $newLiteratur
	 * @return void
	 */
	public function createAction(Literatur $newLiteratur) {
		$this->literaturRepository->add($newLiteratur);
		$this->response->setStatus(201);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Literatur $literatur
	 * @return void
	 */
	public function updateAction(Literatur $literatur) {
		$this->literaturRepository->update($literatur);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Literatur $literatur
	 * @return void
	 */
	public function deleteAction(Literatur $literatur) {
		$this->literaturRepository->remove($literatur);
	}

}

?>