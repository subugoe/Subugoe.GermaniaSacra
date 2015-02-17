<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus;

class BearbeitungsstatusController extends AbstractBaseController {

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
	 * @var string
	 */
	const  start = 0;

	/**
	 * @var string
	 */
	const  length = 100;

	/**
	 * Returns the list of all Bearbeitungsstatus entities
	 * @FLOW\SkipCsrfProtection
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('bearbeitungsstatus'));
		}
		$searchArr = array();
		if ($this->request->hasArgument('columns'))  {
			$columns = $this->request->getArgument('columns');
			foreach ($columns as $column) {
				if (!empty($column['data']) && !empty($column['search']['value'])) {
					$searchArr[$column['data']] = $column['search']['value'];
				}
			}
		}
		if ($this->request->hasArgument('order')) {
			$order = $this->request->getArgument('order');
			if (!empty($order)) {
				$orderDir = $order[0]['dir'];
				$orderById = $order[0]['column'];
				if (!empty($orderById)) {
					$columns = $this->request->getArgument('columns');
					$orderBy = $columns[$orderById]['data'];
				}
			}
		}
		if ($this->request->hasArgument('draw')) {
			$draw = $this->request->getArgument('draw');
		}
		else {
			$draw = 0;
		}
		$start = $this->request->hasArgument('start') ? $this->request->getArgument('start'):self::start;
		$length = $this->request->hasArgument('length') ? $this->request->getArgument('length'):self::length;
		if (empty($searchArr)) {
			if ((isset($orderBy) && !empty($orderBy)) && (isset($orderDir) && !empty($orderDir))) {
				if ($orderDir === 'asc') {
					$orderArr = array($orderBy => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING);
				}
				elseif ($orderDir === 'desc') {
					$orderArr = array($orderBy => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING);
				}
			}
			if (isset($orderArr) && !empty($orderArr)) {
				$orderings = $orderArr;
			}
			else {
				$orderings = array('name' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING);
			}
			$bearbeitungsstatus = $this->bearbeitungsstatusRepository->getCertainNumberOfBearbeitungsstatus($start, $length, $orderings);
			$recordsTotal = $this->bearbeitungsstatusRepository->getNumberOfEntries();
			$recordsFiltered = $recordsTotal;
		}
		else {
			if ((isset($orderBy) && !empty($orderBy)) && (isset($orderDir) && !empty($orderDir))) {
				if ($orderDir === 'asc') {
					$orderArr = array($orderBy, 'ASC');
				}
				elseif ($orderDir === 'desc') {
					$orderArr = array($orderBy, 'DESC');
				}
			}
			if (isset($orderArr) && !empty($orderArr)) {
				$orderings = $orderArr;
			}
			else {
				$orderings = array('name', 'ASC');
			}
			$bearbeitungsstatus = $this->bearbeitungsstatusRepository->searchCertainNumberOfBearbeitungsstatus($start, $length, $orderings, $searchArr, 1);
			$recordsFiltered = $this->bearbeitungsstatusRepository->searchCertainNumberOfBearbeitungsstatus($start, $length, $orderings, $searchArr, 2);
			$recordsTotal = $this->bearbeitungsstatusRepository->getNumberOfEntries();
		}
		if (!isset($recordsFiltered)) {
			$recordsFiltered = $recordsTotal;
		}
		$this->view->assign('bearbeitungsstatus', ['data' => $bearbeitungsstatus, 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
	}

	/**
	 * Create a new Bearbeitungsstatus entity
	 * @return void
	 */
	public function createAction() {
		$bearbeitungsstatusObj = new Bearbeitungsstatus();
		if (is_object($bearbeitungsstatusObj)) {
			if (!$this->request->hasArgument('name')) {
				$this->throwStatus(400, 'Bearbeitungsstatus name not provided', NULL);
			}
			$bearbeitungsstatusObj->setName($this->request->getArgument('name'));
			$this->bearbeitungsstatusRepository->add($bearbeitungsstatusObj);
			$this->persistenceManager->persistAll();
			$this->clearCachesFor('bearbeitungsstatus');
			$this->throwStatus(201, NULL, NULL);
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
			$this->throwStatus(400, 'Required uUID not provided', NULL);
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
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
		if (is_object($bearbeitungsstatusObj)) {
			$bearbeitungsstatusObj->setName($this->request->getArgument('name'));
			$this->bearbeitungsstatusRepository->update($bearbeitungsstatusObj);
			$this->persistenceManager->persistAll();
			$this->clearCachesFor('bearbeitungsstatus');

			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Entity Bearbeitungsstatus not available', NULL);
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
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$klosters = count($this->klosterRepository->findByBearbeitungsstatus($uuid));
		if ($klosters == 0) {
			$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
			if (!is_object($bearbeitungsstatusObj)) {
				$this->throwStatus(400, 'Entity Bearbeitungsstatus not available', NULL);
			}
			$this->bearbeitungsstatusRepository->remove($bearbeitungsstatusObj);
			$this->clearCachesFor('bearbeitungsstatus');
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Due to dependencies Bearbeitungsstatus entity could not be deleted', NULL);
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
			$this->throwStatus(400, 'Required data arguemnts not provided', NULL);
		}
		foreach ($bearbeitungsstatuslist as $uuid => $bearbeitungsstatus) {
			$bearbeitungsstatusObj = $this->bearbeitungsstatusRepository->findByIdentifier($uuid);
			$bearbeitungsstatusObj->setName($bearbeitungsstatus['name']);
			$this->bearbeitungsstatusRepository->update($bearbeitungsstatusObj);
		}
		$this->persistenceManager->persistAll();
		$this->clearCachesFor('bearbeitungsstatus');
		$this->throwStatus(200, NULL, NULL);
	}
}

?>