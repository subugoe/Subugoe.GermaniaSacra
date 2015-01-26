<?php
namespace Subugoe\GermaniaSacra\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Subugoe.GermaniaSacra". *
 *                                                                        *
 *                                                                        */

use Subugoe\GermaniaSacra\Controller\DataImportController;
use Subugoe\GermaniaSacra\Controller\DataExportController;
use Subugoe\GermaniaSacra\Utility\JsonGeneratorUtility;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerFactory;
/**
 * GermaniaSacra command controller
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

	public function initializeAction() {
		$this->gitUserToken = $this->settings['git']['token'];
		$this->accessDumpHash = $this->settings['git']['accessDumpHash'];
		$this->citekeysHash = $this->settings['git']['citekeysHash'];
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
		$this->logger->log('Data import may take well over 30 minutes depending on the environment used. Do not exit.');
		/** @var DataImportController $importer */
		$importer = new DataImportController($this->logger, $this->settings);
		$importer->access2mysqlAction();
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

	/**
	 * @return void
	 */
	public function alisInkKlosterImportCommand() {
		/** @var DataImportController $importer */
		$importer = new DataImportController($this->logger, $this->settings);
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET unique_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$importer->delAccessKlosterTabAction();
		$importer->importAccessInkKlosterDataAction();
		$importer->importKlosterAction();
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
		$this->logger->log('Import completed in ' . $time . ' seconds.');
	}

	/**
	 * Generates json file from an entity
	 *
	 * @param string $entityName
	 * @return void
	 */
	public function jsonCommand($entityName) {
		JsonGeneratorUtility::generateJsonFile($entityName);
	}

}