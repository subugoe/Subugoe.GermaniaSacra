<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus;

class PersonallistenstatusController extends AbstractBaseController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\PersonallistenstatusRepository
	 */
	protected $personallistenstatusRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

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
	const  start = 0;

	/**
	 * @var string
	 */
	const  length = 100;

	/**
	 * Returns the list of all Personallistenstatus entities
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('personallistenstatus'));
		}
		$start = $this->request->hasArgument('start') ? $this->request->getArgument('start'):self::start;
		$length = $this->request->hasArgument('length') ? $this->request->getArgument('length'):self::length;
		$personallistenstatus = $this->personallistenstatusRepository->getCertainNumberOfPersonallistenstatus($start, $length);
		$this->view->assign('personallistenstatus', ['data' => $personallistenstatus]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
		return $this->view->render();
	}

	/**
	 * Create a new Personallistenstatus entity
	 * @return void
	 */
	public function createAction() {
		$personallistenstatusObj = new Personallistenstatus();
		if (is_object($personallistenstatusObj)) {
			if (!$this->request->hasArgument('name')) {
				$this->throwStatus(400, 'Personallistenstatus name not provided', NULL);
			}
			$personallistenstatusObj->setName($this->request->getArgument('name'));
			$this->personallistenstatusRepository->add($personallistenstatusObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, NULL);
		}
	}

	/**
	 * Edit a Personallistenstatus entity
	 * @return array $personallistenstatusArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$personallistenstatusArr = array();
		$personallistenstatusObj = $this->personallistenstatusRepository->findByIdentifier($uuid);
		$personallistenstatusArr['uUID'] = $personallistenstatusObj->getUUID();
		$personallistenstatusArr['name'] = $personallistenstatusObj->getName();
		return json_encode($personallistenstatusArr);
	}

	/**
	 * Update a Personallistenstatus entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$personallistenstatusObj = $this->personallistenstatusRepository->findByIdentifier($uuid);
		if (is_object($personallistenstatusObj)) {
			$personallistenstatusObj->setName($this->request->getArgument('name'));
			$this->personallistenstatusRepository->update($personallistenstatusObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Entity Personallistenstatus not available', NULL);
		}
	}

	/**
	 * Delete a Personallistenstatus entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$klosters = count($this->klosterRepository->findByPersonallistenstatus($uuid));
		if ($klosters == 0) {
			$personallistenstatusObj = $this->personallistenstatusRepository->findByIdentifier($uuid);
			if (!is_object($personallistenstatusObj)) {
				$this->throwStatus(400, 'Entity Personallistenstatus not available', NULL);
			}
			$this->personallistenstatusRepository->remove($personallistenstatusObj);
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Due to dependencies Personallistenstatus entity could not be deleted', NULL);
		}
	}

	/**
	 * Update a list of Personallistenstatus entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$personallistenstatuslist = $this->request->getArgument('data');
		}
		if (empty($personallistenstatuslist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', NULL);
		}
		foreach ($personallistenstatuslist as $uuid => $personallistenstatus) {
			$personallistenstatusObj = $this->personallistenstatusRepository->findByIdentifier($uuid);
			$personallistenstatusObj->setName($personallistenstatus['name']);
			$this->personallistenstatusRepository->update($personallistenstatusObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, NULL);
	}
}

?>