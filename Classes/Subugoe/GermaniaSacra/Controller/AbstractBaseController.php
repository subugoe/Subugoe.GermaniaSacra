<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

/**
* An action controller with base functionality for all action controllers
*/
abstract class AbstractBaseController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeiterRepository
	 */
	protected $bearbeiterRepository;

	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter
	 */
	protected $bearbeiterObj;

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * @return void
	 */
	public function initializeAction() {
		$account = $this->securityContext->getAccount();
		$this->bearbeiterObj = $this->bearbeiterRepository->findOneByAccount($account);
	}

}
?>