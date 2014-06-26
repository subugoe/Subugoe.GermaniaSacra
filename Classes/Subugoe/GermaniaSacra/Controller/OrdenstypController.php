<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Ort;

class OrdenstypController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenstypRepository
	 */
	protected $ordenstypRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('ordenstyp', $this->ordenstypRepository->findAll());
	}

}

?>