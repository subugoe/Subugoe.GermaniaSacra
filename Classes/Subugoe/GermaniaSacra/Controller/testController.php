<?php
namespace Subugoe\GermaniaSacra\Controller;

/*                                                                        *

 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\RestController;

use Subugoe\GermaniaSacra\Domain\Model\Kloster;

class TestController extends RestController {

 
	protected $defaultViewObjectName = 'TYPO3\Flow\Mvc\View\JsonView';
  
	protected $resourceArgumentName = 'kloster';

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterstandortRepository
	 */
	protected $klosterstandortRepository;

		
	/**
	 * @return void
	 */
	public function listAction() {
		$this->view->setVariablesToRender(array('klosters'));
        $this->view->assign('klosters', $this->klosterRepository->findAll());
	}

	/**
	 * @return void
	 */
	public function testAction() {

	}	

}

?>