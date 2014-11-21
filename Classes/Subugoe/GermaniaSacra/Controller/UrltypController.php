<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Urltyp;

class UrltypController extends AbstractBaseController {

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
			$this->view->setVariablesToRender(array('urltyp'));
		}
		$this->view->assign('urltyp', ['data' => $this->urltypRepository->findAll()]);
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
	}

	/**
	 * Create a new Urltyp entity
	 * @return void
	 */
	public function createAction() {
		$urltypObj = new Urltyp();
		if (is_object($urltypObj)) {
			if (!$this->request->hasArgument('name')) {
				$this->throwStatus(400, 'Url name not provided', NULL);
			}
			$urltypObj->setName($this->request->getArgument('name'));
			$this->urltypRepository->add($urltypObj);
			$this->persistenceManager->persistAll();
			$this->clearCachesFor('urlyp');

			$this->throwStatus(201, NULL, NULL);
		}
	}

	/**
	 * Edit an Urltyp entity
	 * @return array $urltypArr
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$urltypArr = array();
		$urltypObj = $this->urltypRepository->findByIdentifier($uuid);
		$urltypArr['uUID'] = $urltypObj->getUUID();
		$urltypArr['name'] = $urltypObj->getName();
		return json_encode($urltypArr);
	}

	/**
	 * Update an Urltyp entity
	 * @return void
	 */
	public function updateAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$urltypObj = $this->urltypRepository->findByIdentifier($uuid);
		if (is_object($urltypObj)) {
			$urltypObj->setName($this->request->getArgument('name'));
			$this->urltypRepository->update($urltypObj);
			$this->persistenceManager->persistAll();
			$this->clearCachesFor('urlyp');

			$this->throwStatus(200, NULL, NULL);
		}
		else {
			$this->throwStatus(400, 'Entity Urltyp not available', NULL);
		}
	}

	/**
	 * Delete an Urltyp entity
	 * @return void
	 */
	public function deleteAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', NULL);
		}
		$urls = count($this->urlRepository->findByUrltyp($uuid));
		if ($urls == 0) {
			$urltypObj = $this->urltypRepository->findByIdentifier($uuid);
			if (!is_object($urltypObj)) {
				$this->throwStatus(400, 'Entity Urltyp not available', NULL);
			}
			$this->urltypRepository->remove($urltypObj);
			$this->clearCachesFor('urlyp');

			$this->throwStatus(200, NULL, NULL);
		}
		else {
			$this->throwStatus(400, 'Due to dependencies Urltyp entity could not be deleted', NULL);
		}
	}

	/**
	 * Update a list of Urltyp entities
	 * @return void
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$urltyplist = $this->request->getArgument('data');
		}
		if (empty($urltyplist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', NULL);
		}
		foreach ($urltyplist as $uuid => $urltyp) {
			$urltypObj = $this->urltypRepository->findByIdentifier($uuid);
			$urltypObj->setName($urltyp['name']);
			$this->urltypRepository->update($urltypObj);
		}
		$this->persistenceManager->persistAll();
		$this->clearCachesFor('urlyp');

		$this->throwStatus(200, NULL, NULL);
	}
}
?>