<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Land;

class LandController extends AbstractBaseController
{
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
    protected $supportedMediaTypes = ['text/html', 'application/json'];

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = [
            'json' => 'TYPO3\\Flow\\Mvc\\View\\JsonView',
            'html' => 'TYPO3\\Fluid\\View\\TemplateView'
    ];

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
     */
    public function listAction()
    {
        if ($this->request->getFormat() === 'json') {
            $this->view->setVariablesToRender(['land']);
        }
        $searchArr = [];
        if ($this->request->hasArgument('columns')) {
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
        } else {
            $draw = 0;
        }
        $start = $this->request->hasArgument('start') ? $this->request->getArgument('start'):self::start;
        $length = $this->request->hasArgument('length') ? $this->request->getArgument('length'):self::length;
        if (empty($searchArr)) {
            if ((isset($orderBy) && !empty($orderBy)) && (isset($orderDir) && !empty($orderDir))) {
                if ($orderDir === 'asc') {
                    $orderArr = [$orderBy => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING];
                } elseif ($orderDir === 'desc') {
                    $orderArr = [$orderBy => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING];
                }
            }
            if (isset($orderArr) && !empty($orderArr)) {
                $orderings = $orderArr;
            } else {
                $orderings = ['land' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING];
            }
            $land = $this->landRepository->getCertainNumberOfLand($start, $length, $orderings);
            $recordsTotal = $this->landRepository->getNumberOfEntries();
            $recordsFiltered = $recordsTotal;
        } else {
            if ((isset($orderBy) && !empty($orderBy)) && (isset($orderDir) && !empty($orderDir))) {
                if ($orderDir === 'asc') {
                    $orderArr = [$orderBy, 'ASC'];
                } elseif ($orderDir === 'desc') {
                    $orderArr = [$orderBy, 'DESC'];
                }
            }
            if (isset($orderArr) && !empty($orderArr)) {
                $orderings = $orderArr;
            } else {
                $orderings = ['land', 'ASC'];
            }
            $land = $this->landRepository->searchCertainNumberOfLand($start, $length, $orderings, $searchArr, 1);
            $recordsFiltered = $this->landRepository->searchCertainNumberOfLand($start, $length, $orderings, $searchArr, 2);
            $recordsTotal = $this->landRepository->getNumberOfEntries();
        }
        if (!isset($recordsFiltered)) {
            $recordsFiltered = $recordsTotal;
        }
        $this->view->assign('land', ['data' => $land, 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered]);
        $this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
        return $this->view->render();
    }

    /**
     * Create a new Land entity
     */
    public function createAction()
    {
        $landObj = new Land();
        if (is_object($landObj)) {
            if (!$this->request->hasArgument('land')) {
                $this->throwStatus(400, 'Land name not provided', null);
            }
            $landObj->setLand($this->request->getArgument('land'));
            $landObj->setIst_in_deutschland($this->request->hasArgument('ist_in_deutschland'));
            $this->landRepository->add($landObj);
            $this->persistenceManager->persistAll();
            $this->throwStatus(201, null, null);
        }
    }

    /**
     * Edit a Land entity
     * @return array $landArr
     */
    public function editAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $landArr = [];
        $landObj = $this->landRepository->findByIdentifier($uuid);
        $landArr['uUID'] = $landObj->getUUID();
        $landArr['land'] = $landObj->getLand();
        $landArr['ist_in_deutschland'] = $landObj->getIst_in_deutschland();
        return json_encode($landArr);
    }

    /**
     * Update a Land entity
     */
    public function updateAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $landObj = $this->landRepository->findByIdentifier($uuid);
        if (is_object($landObj)) {
            $landObj->setLand($this->request->getArgument('land'));
            $landObj->setIst_in_deutschland($this->request->hasArgument('ist_in_deutschland'));
            $this->landRepository->update($landObj);
            $this->persistenceManager->persistAll();
            $this->throwStatus(200, null, null);
        } else {
            $this->throwStatus(400, 'Entity Land not available', null);
        }
    }

    /**
     * Delete a Land entity
     */
    public function deleteAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $lands = count($this->ortRepository->findByLand($uuid));
        if ($lands == 0) {
            $landObj = $this->landRepository->findByIdentifier($uuid);
            if (!is_object($landObj)) {
                $this->throwStatus(400, 'Entity Land not available', null);
            }
            $this->landRepository->remove($landObj);
            $this->throwStatus(200, null, null);
        } else {
            $this->throwStatus(400, 'Due to dependencies Land entity could not be deleted', null);
        }
    }

    /**
     * Update a list of Land entities
     */
    public function updateListAction()
    {
        if ($this->request->hasArgument('data')) {
            $landlist = $this->request->getArgument('data');
        }
        if (empty($landlist)) {
            $this->throwStatus(400, 'Required data arguemnts not provided', null);
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
        $this->throwStatus(200, null, null);
    }
}
