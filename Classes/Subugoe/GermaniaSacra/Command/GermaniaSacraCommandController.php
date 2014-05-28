<?php
namespace Subugoe\GermaniaSacra\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Surf".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

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
	 * @var string
	 */
	protected $dumpDirectory;

	protected $readPrefix = 'tbl';

	public function __construct() {
		parent::__construct();
		$this->dumpDirectory = FLOW_PATH_ROOT . '/Build/GermaniaSacra/Access';
	}


	public function alisImportCommand() {
		$importer = new \Subugoe\GermaniaSacra\Controller\DataImportController();
		$importer->access2mysqlAction();
	}

	/**
	 * import Access SQL Dump into Flow database structure
	 *
	 * @return void
	 */
	public function importAccessCommand() {
		$logger = new \TYPO3\Flow\Log\Logger();
		$dumpFileName = 'klosterdatenbankdump.sql';

		if (!is_dir($this->dumpDirectory)) {
			throw new \TYPO3\Flow\Resource\Exception;
		}
		if (!file_exists($this->dumpDirectory . '/' . $dumpFileName)) {
			throw new \TYPO3\Flow\Resource\Exception(1398846324);
		}

		$sql = file_get_contents($this->dumpDirectory . '/' . $dumpFileName);
		$sql = str_replace('CREATE DATABASE IF NOT EXISTS `Klosterdatenbank`;', '', $sql);
		$sql = str_replace('USE `Klosterdatenbank`;', '', $sql);
		$sqlConnection = $this->entityManager->getConnection();
		$sqlConnection->executeUpdate($sql);

		/** @var array $citeKeys */
		$citeKeys = $this->getCiteKeys();

		/** @var array $bistumUrl */
		$bistumUrl = $this->getBistumUrl();

	}

	/**
	 * @return array
	 */
	protected function getCiteKeys() {
		$citeKeyFile = $this->dumpDirectory . '/GS-citekeys.csv';
		return $csv = array_map('str_getcsv', file($citeKeyFile));
	}

	/**
	 * @return array
	 */
	protected function getBistumUrl() {
		/** @var Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'Select * FROM ' . $this->readPrefix . 'Bistum';
		$bistums = $sqlConnection->fetchAll($sql);

		$bistumDict = array();
		foreach ($bistums as $row) {
			$istErzbistum = ($row['ErzbistumAuswahlfeld'] === 'Erzbistum');

			$bistum = array(
					'uid' => $row['ID'],
					'bistum' => $row['Bistum'],
					'kirchenprovinz' => $row['Kirchenprovinz'],
					'bemerkung' => $row['Bemerkung'],
					'ist_erzbistum' => $istErzbistum,
					'shapefile' => $row['Shapefile'],
					'ort_uid' => $row['Bistumssitz']

			);
			array_push($bistumDict, $bistum);
		}
		return $bistumDict;
	}
}

?>