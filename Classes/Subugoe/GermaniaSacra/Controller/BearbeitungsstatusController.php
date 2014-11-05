<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus;
use TYPO3\Flow\Mvc\Controller\ActionController;

class BearbeitungsstatusController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeitungsstatusRepository
	 */
	protected $bearbeitungsstatusRepository;

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
	 * @return void
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('bearbeitungsstatuses'));
		}
		$this->view->assign('bearbeitungsstatuses', ['data' => $this->bearbeitungsstatusRepository->findAll()]);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function showAction(Bearbeitungsstatus $bearbeitungsstatus) {
		$this->view->setVariablesToRender(array('bearbeitungsstatus'));
		$this->view->assign('bearbeitungsstatus', $bearbeitungsstatus);
	}

	/**
	 * Create a new Bearbeitungsstatus entity
	 * @return void
	 */
	public function createAction() {
		$bearbeitungsstatusObj = new Bearbeitungsstatus();
		if (is_object($bearbeitungsstatusObj)) {
			if (!$this->request->hasArgument('name')) {
				$this->throwStatus(400, 'Bearbeitungsstatus name not provided', Null);
			}
			$bearbeitungsstatusObj->setName($this->request->getArgument('name'));
			$this->bearbeitungsstatusRepository->add($bearbeitungsstatusObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, Null);
		}
	}

	/**
	 * Edit a Bearbeitungsstatus entity
	 * @return array $bearbeitungsstatusArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$bearbeitungsstatusArr = array();
		$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
		$bearbeitungsstatusArr['uUID'] = $bearbeitungsstatusObj->getUUID();
		$bearbeitungsstatusArr['name'] = $bearbeitungsstatusObj->getName();
		return json_encode($bearbeitungsstatusArr);
	}

	/**
	 * Update a Bearbeitungsstatus entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
		if (is_object($bearbeitungsstatusObj)) {
			$bearbeitungsstatusObj->setName($this->request->getArgument('name'));
			$this->bearbeitungsstatusRepository->update($bearbeitungsstatusObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Entity Bearbeitungsstatus not available', Null);
		}
	}

	/**
	 * Delete a Bearbeitungsstatus entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$klosters = count($this->klosterRepository->findByBearbeitungsstatus($uuid));
		if ($klosters == 0) {
			$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
			if (!is_object($bearbeitungsstatusObj)) {
				$this->throwStatus(400, 'Entity Bearbeitungsstatus not available', Null);
			}
			$this->bearbeitungsstatusRepository->remove($bearbeitungsstatusObj);
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Due to dependencies Bearbeitungsstatus entity could not be deleted', Null);
		}
	}

	/**
	 * Update a list of Bearbeitungsstatus entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$bearbeitungsstatuslist = $this->request->getArgument('data');
		}
		if (empty($bearbeitungsstatuslist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', Null);
		}
		foreach ($bearbeitungsstatuslist as $uuid => $bearbeitungsstatus) {
			$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
			$bearbeitungsstatusObj->setName($bearbeitungsstatus['name']);
			$this->bearbeitungsstatusRepository->update($bearbeitungsstatusObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, Null);
	}
}
?>