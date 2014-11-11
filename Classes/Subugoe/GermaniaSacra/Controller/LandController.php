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
	* @return void
	*/
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('land'));
		}
		$this->view->assign('land', ['data' => $this->landRepository->findAll()]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
	}

	/**
	 * Create a new Land entity
	 * @return void
	 */
	public function createAction() {
		$landObj = new Land();
		if (is_object($landObj)) {
			if (!$this->request->hasArgument('land')) {
				$this->throwStatus(400, 'Land name not provided', Null);
			}
			$landObj->setLand($this->request->getArgument('land'));
			$landObj->setIst_in_deutschland($this->request->hasArgument('ist_in_deutschland'));
			$this->landRepository->add($landObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(201, NULL, Null);
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
			$this->throwStatus(400, 'Required uUID not provided', Null);
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
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$landObj = $this->landRepository->findByIdentifier($uuid);
		if (is_object($landObj)) {
			$landObj->setLand($this->request->getArgument('land'));
			$landObj->setIst_in_deutschland($this->request->hasArgument('ist_in_deutschland'));
			$this->landRepository->update($landObj);
			$this->persistenceManager->persistAll();
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Entity Land not available', Null);
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
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$lands = count($this->ortRepository->findByLand($uuid));
		if ($lands == 0) {
			$landObj = $this->landRepository->findByIdentifier($uuid);
			if (!is_object($landObj)) {
				$this->throwStatus(400, 'Entity Land not available', Null);
			}
			$this->landRepository->remove($landObj);
			$this->throwStatus(200, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Due to dependencies Land entity could not be deleted', Null);
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
			$this->throwStatus(400, 'Required data arguemnts not provided', Null);
		}
		foreach ($landlist as $uuid => $land) {
			$landObj = $this->landRepository->findByIdentifier($uuid);
			$landObj->setLand($land['land']);
			if (isset($land['ist_in_deutschland']) && !empty($land['ist_in_deutschland'])) {
				$ist_in_deutschland = $land['ist_in_deutschland'];
			}
			else {
				$ist_in_deutschland = 0;
			}
			$landObj->setIst_in_deutschland($ist_in_deutschland);
			$this->landRepository->update($landObj);
		}
		$this->persistenceManager->persistAll();
		$this->throwStatus(200, NULL, Null);
	}
}
?>