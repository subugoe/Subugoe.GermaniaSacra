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

	public function alisImportCommand() {

		$log = new \TYPO3\Flow\Log\LoggerFactory();
		$logger = $log->create('GermaniaSacra', 'TYPO3\Flow\Log\Logger', '\TYPO3\Flow\Log\Backend\ConsoleBackend');

		$importer = new \Subugoe\GermaniaSacra\Controller\DataImportController($logger);

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
	}

}

?>