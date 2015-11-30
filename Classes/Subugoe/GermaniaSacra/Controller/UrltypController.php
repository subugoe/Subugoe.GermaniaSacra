<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Urltyp;

class UrltypController extends AbstractBaseController
{
    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\UrltypRepository
     */
    protected $urltypRepository;

    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\UrlRepository
     */
    protected $urlRepository;

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
     * Returns the list of all Urltyp entities
     */
    public function listAction()
    {
        if ($this->request->getFormat() === 'json') {
            $this->view->setVariablesToRender(['urltyp']);
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
                $orderings = ['name' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING];
            }
            $urltyp = $this->urltypRepository->getCertainNumberOfUrltyp($start, $length, $orderings);
            $recordsTotal = $this->urltypRepository->getNumberOfEntries();
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
                $orderings = ['name', 'ASC'];
            }
            $urltyp = $this->urltypRepository->searchCertainNumberOfLand($start, $length, $orderings, $searchArr, 1);
            $recordsFiltered = $this->urltypRepository->searchCertainNumberOfLand($start, $length, $orderings, $searchArr, 2);
            $recordsTotal = $this->urltypRepository->getNumberOfEntries();
        }
        if (!isset($recordsFiltered)) {
            $recordsFiltered = $recordsTotal;
        }
        $this->view->assign('urltyp', ['data' => $urltyp, 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered]);
        $this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
        return $this->view->render();
    }

    /**
     * Create a new Urltyp entity
     */
    public function createAction()
    {
        $urltypObj = new Urltyp();
        if (is_object($urltypObj)) {
            if (!$this->request->hasArgument('name')) {
                $this->throwStatus(400, 'Url name not provided', null);
            }
            $urltypObj->setName($this->request->getArgument('name'));
            $this->urltypRepository->add($urltypObj);
            $this->persistenceManager->persistAll();
            $this->throwStatus(201, null, null);
        }
    }

    /**
     * Edit an Urltyp entity
     * @return array $urltypArr
     */
    public function editAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $urltypArr = [];
        $urltypObj = $this->urltypRepository->findByIdentifier($uuid);
        $urltypArr['uUID'] = $urltypObj->getUUID();
        $urltypArr['name'] = $urltypObj->getName();
        return json_encode($urltypArr);
    }

    /**
     * Update an Urltyp entity
     */
    public function updateAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $urltypObj = $this->urltypRepository->findByIdentifier($uuid);
        if (is_object($urltypObj)) {
            $urltypObj->setName($this->request->getArgument('name'));
            $this->urltypRepository->update($urltypObj);
            $this->persistenceManager->persistAll();
            $this->throwStatus(200, null, null);
        } else {
            $this->throwStatus(400, 'Entity Urltyp not available', null);
        }
    }

    /**
     * Delete an Urltyp entity
     */
    public function deleteAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $urls = count($this->urlRepository->findByUrltyp($uuid));
        if ($urls == 0) {
            $urltypObj = $this->urltypRepository->findByIdentifier($uuid);
            if (!is_object($urltypObj)) {
                $this->throwStatus(400, 'Entity Urltyp not available', null);
            }
            $this->urltypRepository->remove($urltypObj);
            $this->throwStatus(200, null, null);
        } else {
            $this->throwStatus(400, 'Due to dependencies Urltyp entity could not be deleted', null);
        }
    }

    /**
     * Update a list of Urltyp entities
     */
    public function updateListAction()
    {
        if ($this->request->hasArgument('data')) {
            $urltyplist = $this->request->getArgument('data');
        }
        if (empty($urltyplist)) {
            $this->throwStatus(400, 'Required data arguemnts not provided', null);
        }
        foreach ($urltyplist as $uuid => $urltyp) {
            $urltypObj = $this->urltypRepository->findByIdentifier($uuid);
            $urltypObj->setName($urltyp['name']);
            $this->urltypRepository->update($urltypObj);
        }
        $this->persistenceManager->persistAll();
        $this->throwStatus(200, null, null);
    }
}
