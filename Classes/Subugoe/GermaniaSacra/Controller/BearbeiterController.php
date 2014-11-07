<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeiter;
use TYPO3\Flow\Mvc\Controller\ActionController;

class BearbeiterController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeiterRepository
	 */
	protected $bearbeiterRepository;

	/**
	 * @var \TYPO3\Flow\Security\Policy\PolicyService
	 * @Flow\Inject
	 */
	protected $policyService;

	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @var \TYPO3\Flow\Security\Policy\RoleRepository
	 * @Flow\Inject
	 */
	protected $roleRepository;

	/**
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
			$this->view->setVariablesToRender(array('bearbeiters'));
		}
		$this->view->assign('bearbeiters', ['data' => $this->bearbeiterRepository->findAll()]);
	}

	/**
	 * Create a new Bearbeiter entity
	 * @return void
	 */
	public function createAction() {
		$bearbeiterObj = new Bearbeiter();
		if (is_object($bearbeiterObj)) {
			if (!$this->request->hasArgument('bearbeiter')) {
				$this->throwStatus(400, 'Bearbeiter name not provided', Null);
			}
			$bearbeiterObj->setBearbeiter($this->request->getArgument('bearbeiter'));
			$this->bearbeiterRepository->add($bearbeiterObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, Null);
		}
	}

	/**
	 * Edit a Bearbeiter entity
	 * @return array $bearbeiterArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$bearbeiterArr = array();
		$bearbeiterObj = $this->bearbeiterRepository->findByIdentifier($uuid);
		$bearbeiterArr['uUID'] = $bearbeiterObj->getUUID();
		$bearbeiterArr['bearbeiter'] = $bearbeiterObj->getBearbeiter();
		$bearbeiterArr['role'] = array_keys($this->securityContext->getAccount()->getRoles())[0];
		return json_encode($bearbeiterArr);
	}

	/**
	 * Update a Bearbeiter entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$bearbeiterObj = $this->bearbeiterRepository->findByIdentifier($uuid);
		if (is_object($bearbeiterObj)) {
			$bearbeiterObj->setBearbeiter($this->request->getArgument('bearbeiter'));
			$this->bearbeiterRepository->update($bearbeiterObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Entity Bearbeiter not available', Null);
		}
	}

	/**
	 * Update a list of Bearbeiter entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$bearbeiterlist = $this->request->getArgument('data');
		}
		if (empty($bearbeiterlist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', Null);
		}
		foreach ($bearbeiterlist as $uuid => $bearbeiter) {
			$bearbeiterObj = $this->bearbeiterRepository->findByIdentifier($uuid);
			$bearbeiterObj->setBearbeiter($bearbeiter['bearbeiter']);
			$this->bearbeiterRepository->update($bearbeiterObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, Null);
	}
}
?>