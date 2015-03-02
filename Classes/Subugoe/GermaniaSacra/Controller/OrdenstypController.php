<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Ordenstyp;

class OrdenstypController extends AbstractBaseController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenstypRepository
	 */
	protected $ordenstypRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenRepository
	 */
	protected $ordenRepository;

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
	 * Returns the list of all Ordenstyp entities
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('ordenstyp'));
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
			$ordenstyp = $this->ordenstypRepository->getCertainNumberOfOrdenstyp($start, $length, $orderings);
			$recordsTotal = $this->ordenstypRepository->getNumberOfEntries();
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
			$ordenstyp = $this->ordenstypRepository->searchCertainNumberOfOrdenstyp($start, $length, $orderings, $searchArr, 1);
			$recordsFiltered = $this->ordenstypRepository->searchCertainNumberOfOrdenstyp($start, $length, $orderings, $searchArr, 2);
			$recordsTotal = $this->ordenstypRepository->getNumberOfEntries();
		}
		if (!isset($recordsFiltered)) {
			$recordsFiltered = $recordsTotal;
		}
		$this->view->assign('ordenstyp', ['data' => $ordenstyp, 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
		return $this->view->render();
	}

	/**
	 * Create a new Ordenstyp entity
	 * @return void
	 */
	public function createAction() {
		$ordenstypObj = new Ordenstyp();
		if (is_object($ordenstypObj)) {
			if (!$this->request->hasArgument('ordenstyp')) {
				$this->throwStatus(400, 'Ordenstyp not provided', NULL);
			}
			$ordenstypObj->setOrdenstyp($this->request->getArgument('ordenstyp'));
			$this->ordenstypRepository->add($ordenstypObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, NULL);
		}
	}

	/**
	 * Edit an Ordenstyp entity
	 * @return array $ordenstypArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$ordenstypArr = array();
		$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
		$ordenstypArr['uUID'] = $ordenstypObj->getUUID();
		$ordenstypArr['ordenstyp'] = $ordenstypObj->getOrdenstyp();
		return json_encode($ordenstypArr);
	}

	/**
	 * Update an Ordenstyp entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
		if (is_object($ordenstypObj)) {
			$ordenstypObj->setOrdenstyp($this->request->getArgument('ordenstyp'));
			$this->ordenstypRepository->update($ordenstypObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Entity Ordenstyp not available', NULL);
		}
	}

	/**
	 * Delete an Ordenstyp entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$ordens = count($this->ordenRepository->findByOrdenstyp($uuid));
		if ($ordens == 0) {
			$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
			if (!is_object($ordenstypObj)) {
				$this->throwStatus(400, 'Entity Ordenstyp not available', NULL);
			}
			$this->ordenstypRepository->remove($ordenstypObj);
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Due to dependencies Ordenstyp entity could not be deleted', NULL);
		}
	}

	/**
	 * Update a list of Ordenstyp entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$ordenstyplist = $this->request->getArgument('data');
		}
		if (empty($ordenstyplist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', NULL);
		}
		foreach ($ordenstyplist as $uuid => $ordenstyp) {
			$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
			$ordenstypObj->setOrdenstyp($ordenstyp['ordenstyp']);
			$this->ordenstypRepository->update($ordenstypObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, NULL);
	}
}

?>