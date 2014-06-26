<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Ort;

class LiteraturController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LiteraturRepository
	 */
	protected $literaturRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('literatur', $this->literaturRepository->findAll());
	}

}

?>