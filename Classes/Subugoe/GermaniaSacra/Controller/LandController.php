<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Land;

class LandController extends AbstractBaseController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LandRepository
	 */
	protected $landRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

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
	 * Returns the list of all Land entities
	 * @FLOW\SkipCsrfProtection
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('land'));
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
			$orderings = array('land' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING);
		}
		$recordsTotal = $this->landRepository->getNumberOfEntries();
		$recordsFiltered = $recordsTotal;
		if ($this->request->hasArgument('draw')) {
			$draw = $this->request->getArgument('draw');
		}
		else {
			$draw = 0;
		}
		$start = $this->request->hasArgument('start') ? $this->request->getArgument('start'):self::start;
		$length = $this->request->hasArgument('length') ? $this->request->getArgument('length'):self::length;
		$land = $this->landRepository->getCertainNumberOfLand($start, $length, $orderings);
		$this->view->assign('land', ['data' => $land, 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
		return $this->view->render();
	}

	/**
	 * Create a new Land entity
	 * @return void
	 */
	public function createAction() {
		$landObj = new Land();
		if (is_object($landObj)) {
			if (!$this->request->hasArgument('land')) {
				$this->throwStatus(400, 'Land name not provided', NULL);
			}
			$landObj->setLand($this->request->getArgument('land'));
			$landObj->setIst_in_deutschland($this->request->hasArgument('ist_in_deutschland'));
			$this->landRepository->add($landObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, NULL);
		}
	}

	/**
	 * Edit a Land entity
	 * @return array $landArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$landArr = array();
		$landObj = $this->landRepository->findByIdentifier($uuid);
		$landArr['uUID'] = $landObj->getUUID();
		$landArr['land'] = $landObj->getLand();
		$landArr['ist_in_deutschland'] = $landObj->getIst_in_deutschland();
		return json_encode($landArr);
	}

	/**
	 * Update a Land entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$landObj = $this->landRepository->findByIdentifier($uuid);
		if (is_object($landObj)) {
			$landObj->setLand($this->request->getArgument('land'));
			$landObj->setIst_in_deutschland($this->request->hasArgument('ist_in_deutschland'));
			$this->landRepository->update($landObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Entity Land not available', NULL);
		}
	}

	/**
	 * Delete a Land entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$lands = count($this->ortRepository->findByLand($uuid));
		if ($lands == 0) {
			$landObj = $this->landRepository->findByIdentifier($uuid);
			if (!is_object($landObj)) {
				$this->throwStatus(400, 'Entity Land not available', NULL);
			}
			$this->landRepository->remove($landObj);
			$this->throwStatus(200, NULL, NULL);
		} else {
			$this->throwStatus(400, 'Due to dependencies Land entity could not be deleted', NULL);
		}
	}

	/**
	 * Update a list of Land entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$landlist = $this->request->getArgument('data');
		}
		if (empty($landlist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', NULL);
		}
		foreach ($landlist as $uuid => $land) {
			$landObj = $this->landRepository->findByIdentifier($uuid);
			$landObj->setLand($land['land']);
			if (isset($land['ist_in_deutschland']) && !empty($land['ist_in_deutschland'])) {
				$ist_in_deutschland = $land['ist_in_deutschland'];
			} else {
				$ist_in_deutschland = 0;
			}
			$landObj->setIst_in_deutschland($ist_in_deutschland);
			$this->landRepository->update($landObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, NULL);
	}
}

?>