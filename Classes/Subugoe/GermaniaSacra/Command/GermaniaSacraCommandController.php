<?php
namespace Subugoe\GermaniaSacra\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Surf".            *
 *                                                                        *
 *                                                                        */

use Subugoe\GermaniaSacra\Controller\DataImportController;
use Subugoe\GermaniaSacra\Controller\DataExportController;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerFactory;
/**
 * Surf command controller
 */
class GermaniaSacraCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

	/**
	 * @var \TYPO3\Flow\Log\Logger
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param $settings
	 */
	public function injectSettings($settings) {
		$this->settings = $settings;
	}

	public function __construct() {
		parent::__construct();
		$log = new LoggerFactory();
		$this->logger = $log->create('GermaniaSacra', 'TYPO3\Flow\Log\Logger', '\TYPO3\Flow\Log\Backend\ConsoleBackend');
	}

	/**
	 * @return void
	 */
	public  function alisImportExportCommand() {
		$this->alisImportCommand();
		$this->alisExportCommand();
	}

	/**
	 * @return void
	 */
	public function alisImportCommand() {
		$this->logger->log('Data import may take over 5 minutes. Do not exit.');
		$importer = new DataImportController($this->logger);
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET unique_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$importer->delAccessTabsAction();
		$importer->importAccessAction();
		$importer->emptyTabsAction();
		$importer->importBearbeitungsstatusAction();
		$importer->importBearbeiterAction();
		$importer->importPersonallistenstatusAction();
		$importer->importLandAction();
		$importer->importOrtAction();
		$importer->importBistumAction();
		$importer->importBandAction();
		$importer->importKlosterAction();
		$importer->importKlosterstandortAction();
		$importer->importOrdenAction();
		$importer->importKlosterordenAction();
		$importer->delAccessTabsAction();
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
		$this->logger->log('Import completed in ' . round($time/60, 2) . ' minutes.');
	}

	/**
	 * @return void
	 */
	public function alisExportCommand() {
		/** @var DataExportController $exporter */
		$exporter = new DataExportController($this->logger);
		$exporter->injectSettings($this->settings);
		$exporter->initializeAction();
		$exporter->mysql2solrExportAction();
	}

}

?>