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
	 * @return void
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('ordenstyp'));
		}
		$this->view->assign('ordenstyp', ['data' => $this->ordenstypRepository->findAll()]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
	}

	/**
	 * Create a new Ordenstyp entity
	 * @return void
	 */
	public function createAction() {
		$ordenstypObj = new Ordenstyp();
		if (is_object($ordenstypObj)) {
			if (!$this->request->hasArgument('ordenstyp')) {
				$this->throwStatus(400, 'Ordenstyp not provided', Null);
			}
			$ordenstypObj->setOrdenstyp($this->request->getArgument('ordenstyp'));
			$this->ordenstypRepository->add($ordenstypObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, Null);
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
			$this->throwStatus(400, 'Required uUID not provided', Null);
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
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
		if (is_object($ordenstypObj)) {
			$ordenstypObj->setOrdenstyp($this->request->getArgument('ordenstyp'));
			$this->ordenstypRepository->update($ordenstypObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Entity Ordenstyp not available', Null);
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
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$ordens = count($this->ordenRepository->findByOrdenstyp($uuid));
		if ($ordens == 0) {
			$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
			if (!is_object($ordenstypObj)) {
				$this->throwStatus(400, 'Entity Ordenstyp not available', Null);
			}
			$this->ordenstypRepository->remove($ordenstypObj);
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Due to dependencies Ordenstyp entity could not be deleted', Null);
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
			$this->throwStatus(400, 'Required data arguemnts not provided', Null);
		}
		foreach ($ordenstyplist as $uuid => $ordenstyp) {
			$ordenstypObj = $this->ordenstypRepository->findByIdentifier($uuid);
			$ordenstypObj->setOrdenstyp($ordenstyp['ordenstyp']);
			$this->ordenstypRepository->update($ordenstypObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, Null);
	}
}
?>