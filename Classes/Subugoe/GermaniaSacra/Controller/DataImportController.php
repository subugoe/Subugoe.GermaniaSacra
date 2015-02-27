<?php
namespace Subugoe\GermaniaSacra\Controller;

ini_set('memory_limit', '2048M');

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerFactory;
use TYPO3\Flow\Mvc\Controller\ActionController;

use Subugoe\GermaniaSacra\Domain\Model\Klosterstatus;
use Subugoe\GermaniaSacra\Domain\Model\Ordenstyp;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeiter;
use Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus;
use Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus;
use Subugoe\GermaniaSacra\Domain\Model\Land;
use Subugoe\GermaniaSacra\Domain\Model\Ort;
use Subugoe\GermaniaSacra\Domain\Model\Bistum;
use Subugoe\GermaniaSacra\Domain\Model\Band;
use Subugoe\GermaniaSacra\Domain\Model\Kloster;
use Subugoe\GermaniaSacra\Domain\Model\Urltyp;
use Subugoe\GermaniaSacra\Domain\Model\Url;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\Literatur;
use Subugoe\GermaniaSacra\Domain\Model\Orden;
use Subugoe\GermaniaSacra\Domain\Model\Klosterstandort;
use Subugoe\GermaniaSacra\Domain\Model\Klosterorden;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur;
use Subugoe\GermaniaSacra\Domain\Model\OrdenHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\BandHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\OrtHasUrl;

class DataImportController extends AbstractBaseController {

	/**
	 * @FLOW\Inject
	 * @var \TYPO3\Flow\Security\AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @FLOW\Inject
	 * @var \TYPO3\Flow\Security\AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeiterRepository
	 */
	protected $bearbeiterRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeitungsstatusRepository
	 */
	protected $bearbeitungsstatusRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\PersonallistenstatusRepository
	 */
	protected $personallistenstatusRepository;

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
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumRepository
	 */
	protected $bistumRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BandRepository
	 */
	protected $bandRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

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
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterHasUrlRepository
	 */
	protected $klosterHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterstandortRepository
	 */
	protected $klosterstandortRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterordenRepository
	 */
	protected $klosterordenRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LiteraturRepository
	 */
	protected $literaturRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenRepository
	 */
	protected $ordenRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenstypRepository
	 */
	protected $ordenstypRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenHasUrlRepository
	 */
	protected $ordenHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BandHasUrlRepository
	 */
	protected $bandHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumHasUrlRepository
	 */
	protected $bistumHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtHasUrlRepository
	 */
	protected $ortHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterstatusRepository
	 */
	protected $klosterstatusRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterHasLiteraturRepository
	 */
	protected $klosterHasLiteraturRepository;

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\inject
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

	/**
	 * @var string
	 */
	protected $dumpDirectory;

	/**
	 * @var \TYPO3\Flow\Log\Logger
	 */
	protected $logger;

	/**
	 * @var \TYPO3\Flow\Log\Logger
	 */
	protected $dumpImportlogger;

	/**
	 * @var \TYPO3\Flow\Log\Logger
	 */
	protected $inkDumpImportlogger;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var string
	 */
	protected $accessDumpFilenamePath;

	/**
	 * @var string
	 */
	protected $citekeysFilenamePath;

	/**
	 * @var string
	 */
	protected $inkKlosterDumpFilenamePath;

	/**
	 * @var string
	 */
	protected $cacertFilenamePath;

	/**
	 * @var string
	 */
	protected $cacertSourcePath;

	/**
	 * @var string
	 */
	protected $cacertDestPath;

	/**
	 * @var \Github\Client
	 */
	protected $client;

	/**
	 * @var \Github\Client
	 */
	protected $method;

	/**
	 * @var string
	 */
	const accessDumpFilename = 'klosterdatenbankdump.sql';

	/**
	 * @var string
	 */
	const citekeysFilename = 'GS-citekeys.csv';

	/**
	 * @var string
	 */
	const cacertFilename = 'cacert.pem';

	/**
	 * @var string
	 */
	const inkKlosterDumpFilename = 'inkKlosterDump.sql';

	/**
	 * @var string
	 */
	const cacertSource = 'Packages/Libraries/guzzle/guzzle/src/Guzzle/Http/Resources/';

	/**
	 * @var string
	 */
	const cacertDest = 'Data/Temporary/Production/Cache/Code/Flow_Object_Classes/Resources/';

	/**
	 * @var string
	 */
	const  githubUser = 'subugoe';

	/**
	 * @var string
	 */
	const githubRepository = 'GermaniaSacra-dumps';

	/**
	 * @var string
	 */
	const dumpLogFile = 'Persistent/GermaniaSacra/Log/klosterDumpImport.log';

	/**
	 * Initializes defaults
	 */
	public function initializeAction() {
		parent::initializeAction();
	}

	public function __construct($logger = NULL, $settings = NULL) {
		parent::__construct();
		$this->dumpDirectory = FLOW_PATH_ROOT . 'Data/Persistent/GermaniaSacra/Dump/';
		$this->accessDumpFilenamePath = $this->dumpDirectory . self::accessDumpFilename;
		$this->citekeysFilenamePath = $this->dumpDirectory . self::citekeysFilename;
		$this->inkKlosterDumpFilenamePath = $this->dumpDirectory . self::inkKlosterDumpFilename;
		$this->cacertFilenamePath = FLOW_PATH_ROOT . self::cacertDest . self::cacertFilename;
		$this->cacertSourcePath = FLOW_PATH_ROOT . self::cacertSource . self::cacertFilename;
		$this->cacertDestPath = FLOW_PATH_ROOT . self::cacertDest . self::cacertFilename;
		$this->logger = $logger;
		$this->settings = $settings;
		$this->client = new \Github\Client();
		$this->method = \Github\Client::AUTH_URL_TOKEN;
	}

	public function logAction() {
		$dumpFile = FLOW_PATH_DATA . self::dumpLogFile;
		if (file_exists($dumpFile)) {
			echo nl2br(file_get_contents($dumpFile));
		}
		exit;
	}

	/**
	 * Check Bearbeiter and Account tables for content and acts as appropriate
	 * @return int
	 */
	public function importBearbeiterAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$checkIfBearbeiterTableExists = $sqlConnection->getSchemaManager()->tablesExist('subugoe_germaniasacra_domain_model_bearbeiter');
		if ($checkIfBearbeiterTableExists) {
			$numberOfBearbeiter = count($this->bearbeiterRepository->findAll());
		}
		$checkIfAccountTableExists = $sqlConnection->getSchemaManager()->tablesExist('typo3_flow_security_account');
		if ($checkIfAccountTableExists) {
			$numberOfAccounts = count($this->accountRepository->findAll());
		}
		$nBearbeiter = 0;
		if ($numberOfBearbeiter == 0 && $numberOfAccounts == 0) {
			$nBearbeiter = $this->importAndJoinBearbeiterWithAccount();
		} elseif ($numberOfBearbeiter == 0 && $numberOfAccounts != 0) {
			$sql = 'SET foreign_key_checks = 0';
			$sqlConnection->executeUpdate($sql);
			$accountTbl = 'typo3_flow_security_account';
			$sql = 'DELETE FROM ' . $accountTbl;
			$sqlConnection->executeUpdate($sql);
			$rolesJointTbl = 'typo3_flow_security_account_roles_join';
			$sql = 'DELETE FROM ' . $rolesJointTbl;
			$sqlConnection->executeUpdate($sql);
			$sql = 'SET foreign_key_checks = 1';
			$sqlConnection->executeUpdate($sql);
			$nBearbeiter = $this->importAndJoinBearbeiterWithAccount();
		} elseif ($numberOfBearbeiter != 0 && $numberOfAccounts == 0) {
			$sql = 'SET foreign_key_checks = 0';
			$sqlConnection->executeUpdate($sql);
			$bearbeiterTbl = 'subugoe_germaniasacra_domain_model_bearbeiter';
			$sql = 'DELETE FROM ' . $bearbeiterTbl;
			$sqlConnection->executeUpdate($sql);
			$sql = 'SET foreign_key_checks = 1';
			$sqlConnection->executeUpdate($sql);
			$nBearbeiter = $this->importAndJoinBearbeiterWithAccount();
		}
		return $nBearbeiter;
	}

	/**
	 * Import Bearbeiter table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_bearbeiter and creates an account for every user
	 * @return int
	 */
	private function importAndJoinBearbeiterWithAccount() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT ID, Bearbeiter FROM Bearbeiter ORDER BY ID ASC';
		$bearbeiters = $sqlConnection->fetchAll($sql);
		if (isset($bearbeiters) and is_array($bearbeiters)) {
			$nBearbeiter = 0;
			foreach ($bearbeiters as $be) {
				$uid = $be['ID'];
				$bearbeiter = $be['Bearbeiter'];
				$userName = $this->createUsername($bearbeiter);
				$password = $this->createPassword();
				$account = $this->accountFactory->createAccountWithPassword($userName, $password, array('Flow.Login:Administrator'));
				$this->accountRepository->add($account);
				$bearbeiterObject = new Bearbeiter();
				$bearbeiterObject->setUid($uid);
				$bearbeiterObject->setBearbeiter($bearbeiter);
				$bearbeiterObject->setAccount($account);
				$this->bearbeiterRepository->add($bearbeiterObject);
				$this->persistenceManager->persistAll();
				$this->createUsernamePasswordFile($userName, $password, $uid);
				$nBearbeiter++;
			}
			return $nBearbeiter;
		}
	}

	/**
	 * Import Personallistenstatus table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_personallistenstatus
	 * @return void
	 */
	public function importPersonallistenstatusAction() {
		$personallistenstatusArr = array(1 => 'Erfassung aus den Registern der Germania-Sacra-Bände (in Bearbeitung):',
				2 => 'Die Aufstellung enthält alle Einträge aus den Personallisten des zugehörigen Germania-Sacra-Bandes:',
				3 => 'unvollständig'
		);
		if (isset($personallistenstatusArr) and is_array($personallistenstatusArr)) {
			foreach ($personallistenstatusArr as $key => $name) {
				$personallistenstatusObject = new Personallistenstatus();
				$personallistenstatusObject->setUid($key);
				$personallistenstatusObject->setName($name);
				$this->personallistenstatusRepository->add($personallistenstatusObject);
				$this->persistenceManager->persistAll();
			}
		}
	}

	/**
	 * Import Land table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_land
	 * @return int
	 */
	public function importLandAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT ID_Bundesland, Land, Deutschland FROM Land';
		$Lands = $sqlConnection->fetchAll($sql);
		if (isset($Lands) and is_array($Lands)) {
			$nLand = 0;
			foreach ($Lands as $Land) {
				$uid = $Land['ID_Bundesland'];
				$land = $Land['Land'];
				$ist_in_deutschland = $Land['Deutschland'];
				$landObject = new Land();
				$landObject->setUid($uid);
				$landObject->setLand($land);
				$landObject->setIst_in_deutschland($ist_in_deutschland);
				$this->landRepository->add($landObject);
				$this->persistenceManager->persistAll();
				$nLand++;
			}
			return $nLand;
		}
	}

	/**
	 * Import Ort table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_ort
	 * @return int
	 */
	public function importOrtAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = "ANALYZE LOCAL TABLE " . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sqlConnection->close();
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Geonames"';
		$urltyp = $sqlConnection->fetchAll($sql);
		if (count($urltyp) > 0) {
			$urltypUUID = $urltyp[0]['persistence_object_identifier'];
		}
		if (!isset($urltypUUID)) {
			$urlTypeName = "Geonames";
			$urltypObject = new Urltyp();
			$urltypObject->setName($urlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$urltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM Ort ORDER BY ID ASC';
		$orts = $sqlConnection->fetchAll($sql);
		if (isset($orts) and is_array($orts)) {
			$nOrt = 0;
			foreach ($orts as $ortvalue) {
				$uid = $ortvalue['ID'];
				$ort = $ortvalue['Ort'];
				$laenge = round($ortvalue['Laenge'], 5);
				$breite = round($ortvalue['Breite'], 5);
				$wuestung = $ortvalue['Wuestung'];
				$gemeinde = $ortvalue['Gemeinde'];
				$kreis = $ortvalue['Kreis'];
				$land = $ortvalue['Land'];
				$url = $ortvalue['GeoNameId'];
				$ortObject = new Ort();
				$ortObject->setUid($uid);
				$ortObject->setOrt($ort);
				$ortObject->setLaenge($laenge);
				$ortObject->setBreite($breite);
				$ortObject->setWuestung($wuestung);
				$ortObject->setGemeinde($gemeinde);
				$ortObject->setKreis($kreis);
				$landObject = $this->landRepository->findOneByUid($land);
				if (is_object($landObject)) {
					$ortObject->setLand($landObject);
				}
				$this->ortRepository->add($ortObject);
				$this->persistenceManager->persistAll();
				$ortUUID = $ortObject->getUUID();
				if (isset($url) && !empty($url)) {
					$url = 'http://geonames.org/' . $url;
					$urlbemerkung = $ort . " " . $url;
					$urlObject = new Url();
					$urlObject->setUrl($url);
					$urlObject->setBemerkung($urlbemerkung);
					$urltypObject = $this->urltypRepository->findByIdentifier($urltypUUID);
					$urlObject->setUrltyp($urltypObject);
					$this->urlRepository->add($urlObject);
					$this->persistenceManager->persistAll();
					$urlUUID = $urlObject->getUUID();
					$orthasurlObject = new Orthasurl();
					$ortObject = $this->ortRepository->findByIdentifier($ortUUID);
					$orthasurlObject->setOrt($ortObject);
					$urlObject = $this->urlRepository->findByIdentifier($urlUUID);
					$orthasurlObject->setUrl($urlObject);
					$this->ortHasUrlRepository->add($orthasurlObject);
					$this->persistenceManager->persistAll();
				}
				$nOrt++;
			}
			if ($this->logger) {
				$end = microtime(true);
				$time = number_format(($end - $start), 2);
				$this->logger->log('Ort import completed in ' . round($time / 60, 2) . ' minutes.');
			}
			return $nOrt;
		}
	}

	/**
	 * Import Bistum table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_bistum
	 * @return int
	 */
	public function importBistumAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "GND"';
		$gndurltyp = $sqlConnection->fetchAll($sql);
		if (count($gndurltyp) > 0) {
			$gndurltypUUID = $gndurltyp[0]['persistence_object_identifier'];
		}
		if (!isset($gndurltypUUID)) {
			$gndurlTypeName = "GND";
			$urltypObject = new Urltyp();
			$urltypObject->setName($gndurlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$gndurltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Wikipedia"';
		$wikiurltyp = $sqlConnection->fetchAll($sql);
		if (count($wikiurltyp) > 0) {
			$wikiurltypUUID = $wikiurltyp[0]['persistence_object_identifier'];
		}
		if (!isset($wikiurltypUUID)) {
			$wikiurlTypeName = "Wikipedia";
			$urltypObject = new Urltyp();
			$urltypObject->setName($wikiurlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$wikiurltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM Bistum';
		$Bistums = $sqlConnection->fetchAll($sql);
		if (isset($Bistums) and is_array($Bistums)) {
			$nBistum = 0;
			foreach ($Bistums as $Bistum) {
				$uid = $Bistum['ID'];
				$bistum = $Bistum['Bistum'];
				$kirchenprovinz = $Bistum['Kirchenprovinz'];
				$bemerkung = $Bistum['Bemerkung'];
				$erzbistum = $Bistum['ErzbistumAuswahlfeld'];
				$shapefile = $Bistum['Shapefile'];
				$ort = $Bistum['Bistumssitz'];
				$gnd = $Bistum['GND_Dioezese'];
				$wikipedia = $Bistum['Wikipedia_Dioezese'];
				$bistumObject = new Bistum();
				$bistumObject->setUid($uid);
				$bistumObject->setBistum($bistum);
				$bistumObject->setKirchenprovinz($kirchenprovinz);
				$bistumObject->setBemerkung($bemerkung);
				if ($erzbistum == "Erzbistum") $is_erzbistum = 1;
				else $is_erzbistum = 0;
				$bistumObject->setIst_erzbistum($is_erzbistum);
				$bistumObject->setShapefile($shapefile);
				$ortObject = $this->ortRepository->findOneByUid($ort);
				if (is_object($ortObject)) {
					$bistumObject->setOrt($ortObject);
				}
				$this->bistumRepository->add($bistumObject);
				$this->persistenceManager->persistAll();
				$bistumUUID = $bistumObject->getUUID();
				$sql = 'SELECT * FROM Ort WHERE ID_Bistum=' . $uid . '';
				$ortuids = $sqlConnection->fetchAll($sql);
				if (!empty($ortuids)) {
					foreach ($ortuids as $ortuid) {
						$ort = $this->ortRepository->findOneByUid($ortuid['ID']);
						$ortBistum = $this->bistumRepository->findByIdentifier($bistumUUID);
						$ort->setBistum($ortBistum);
						$this->ortRepository->update($ort);
						$this->persistenceManager->persistAll();
					}
				}
				if (isset($gnd) && !empty($gnd)) {
					$gnd = str_replace("\t", " ", $gnd);
					$gnd = str_replace("http:// ", " ", $gnd);
					$gnd = str_replace(" http", ";http", $gnd);
					$gnd = str_replace(";", "#", $gnd);
					$gnds = explode("#", $gnd);
					if (isset($gnds) && is_array($gnds)) {
						$oldgnd = "";
						foreach ($gnds as $gnd) {
							if (isset($gnd) && !empty($gnd)) {
								if ($gnd != $oldgnd) {
									$gnd = str_replace(" ", "", $gnd);
									$gnd = str_replace("# ", "", $gnd);
									$gndid = str_replace("http://d-nb.info/gnd/", "", $gnd);
									$gndbemerkung = $bistum . " [" . $gndid . "]";
									$urlObject = new Url();
									$urlObject->setUrl($gnd);
									$urlObject->setBemerkung($gndbemerkung);
									$gndurltypObject = $this->urltypRepository->findByIdentifier($gndurltypUUID);
									$urlObject->setUrltyp($gndurltypObject);
									$this->urlRepository->add($urlObject);
									$this->persistenceManager->persistAll();
									$gndurlUUID = $urlObject->getUUID();
									$oldgnd = $gnd;
									$bistumhasurlObject = new Bistumhasurl();
									$bistumObject = $this->bistumRepository->findByIdentifier($bistumUUID);
									$bistumhasurlObject->setBistum($bistumObject);
									$gndurlObject = $this->urlRepository->findByIdentifier($gndurlUUID);
									$bistumhasurlObject->setUrl($gndurlObject);
									$this->bistumHasUrlRepository->add($bistumhasurlObject);
									$this->persistenceManager->persistAll();
								}
							}
						}
					}
				}
				if (isset($wikipedia) && !empty($wikipedia)) {
					$wikipedia = str_replace("http:// ", " ", $wikipedia);
					$wikipedia = str_replace(";", "#", $wikipedia);
					$wikipedias = explode("#", $wikipedia);
					if (isset($wikipedias) && is_array($wikipedias)) {
						$oldwikipedia = "";
						foreach ($wikipedias as $wikipedia) {
							if (isset($wikipedia) && !empty($wikipedia)) {
								if ($wikipedia != $oldwikipedia) {
									$wikipediabemerkung = str_replace("http://de.wikipedia.org/wiki/", "", $wikipedia);
									$wikipediabemerkung = str_replace("_", " ", $wikipediabemerkung);
									$wikipediabemerkung = rawurldecode($wikipediabemerkung);
									$urlObject = new Url();
									$urlObject->setUrl($wikipedia);
									$urlObject->setBemerkung($wikipediabemerkung);
									$wikiurltypObject = $this->urltypRepository->findByIdentifier($wikiurltypUUID);
									$urlObject->setUrltyp($wikiurltypObject);
									$this->urlRepository->add($urlObject);
									$this->persistenceManager->persistAll();
									$wikiurlUUID = $urlObject->getUUID();
									$oldwikipedia = $wikipedia;
									$bistumhasurlObject = new Bistumhasurl();
									$bistumObject = $this->bistumRepository->findByIdentifier($bistumUUID);
									$bistumhasurlObject->setBistum($bistumObject);
									$wikiurlObject = $this->urlRepository->findByIdentifier($wikiurlUUID);
									$bistumhasurlObject->setUrl($wikiurlObject);
									$this->bistumHasUrlRepository->add($bistumhasurlObject);
									$this->persistenceManager->persistAll();
								}
							}
						}
					}
				}
				$nBistum++;
			}
		}
		if ($this->logger) {
			$end = microtime(true);
			$time = number_format(($end - $start), 2);
			$this->logger->log('Bistum import completed in ' . round($time / 60, 2) . ' minutes.');
		}
		return $nBistum;
	}

	/**
	 * Import Band table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_band
	 * @return int
	 */
	public function importBandAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Handle"';
		$handleurltyp = $sqlConnection->fetchAll($sql);
		if (count($handleurltyp) > 0) {
			$handleurltypUUID = $handleurltyp[0]['persistence_object_identifier'];
		}
		if (!isset($handleurltypUUID)) {
			$handleurlTypeName = "Handle";
			$urltypObject = new Urltyp();
			$urltypObject->setName($handleurlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$handleurltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Findpage"';
		$findpageurltyp = $sqlConnection->fetchAll($sql);
		if (count($findpageurltyp) > 0) {
			$findpageurltypUUID = $findpageurltyp[0]['persistence_object_identifier'];
		}
		if (!isset($findpageurltypUUID)) {
			$findpageurlTypeName = "Findpage";
			$urltypObject = new Urltyp();
			$urltypObject->setName($findpageurlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$findpageurltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Dokument"';
		$documenturltyp = $sqlConnection->fetchAll($sql);
		if (count($documenturltyp) > 0) {
			$documenturltypUUID = $documenturltyp[0]['persistence_object_identifier'];
		}
		if (!isset($documenturltypUUID)) {
			$documenturlTypeName = "Dokument";
			$urltypObject = new Urltyp();
			$urltypObject->setName($documenturlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$documenturltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM Band';
		$Bands = $sqlConnection->fetchAll($sql);
		$urltypArr = array();
		if (isset($Bands) and is_array($Bands)) {
			$nBand = 0;
			foreach ($Bands as $Band) {
				$uid = $Band['ID_GSBand'];
				$nummer = $Band['Bandnummer'];
				$sortierung = $Band['Sortierung'];
				$titel = $Band['Kurztitel'];
				$kurztitel = $Band['KurztitelFacette'];
				$bistum = $Band['Bistum'];
				$handle = $Band['handle'];
				$findpage = $Band['findpage'];
				$bandObject = new Band();
				$bandObject->setUid($uid);
				$bandObject->setNummer($nummer);
				$bandObject->setSortierung($sortierung);
				$bandObject->setTitel($titel);
				$bandObject->setKurztitel($kurztitel);
				$bistumObject = $this->bistumRepository->findOneByUid($bistum);
				if (is_object($bistumObject)) {
					$bandObject->setBistum($bistumObject);
				}
				$this->bandRepository->add($bandObject);
				$this->persistenceManager->persistAll();
				$bandUUID = $bandObject->getUUID();
				$urlString = $Band['url'];
				$buchtitel = 'Germania Sacra ' . $nummer . ': ' . $titel;
				if (isset($urlString) && !empty($urlString)) {
					$urlString = trim($urlString, "# ");
					$urls = explode("#", $urlString);
					$url = $urls[0];
					$url = trim($url);
					$url = trim($url, '# ');
					$urlObject = new Url();
					$urlObject->setUrl($url);
					$urlObject->setBemerkung($buchtitel);
					$documenturlObject = $this->urltypRepository->findByIdentifier($documenturltypUUID);
					$urlObject->setUrltyp($documenturlObject);
					$this->urlRepository->add($urlObject);
					$this->persistenceManager->persistAll();
					$urlUUID = $urlObject->getUUID();
					$bandhasurlObject = new Bandhasurl();
					$BandObject = $this->bandRepository->findByIdentifier($bandUUID);
					$bandhasurlObject->setBand($BandObject);
					$urlObject = $this->urlRepository->findByIdentifier($urlUUID);
					$bandhasurlObject->setUrl($urlObject);
					$this->bandHasUrlRepository->add($bandhasurlObject);
					$this->persistenceManager->persistAll();
				}
				if (isset($handle) && !empty($handle)) {
					$handle = trim($handle, "#");
					$urlObject = new Url();
					$urlObject->setUrl($handle);
					$urlObject->setBemerkung($buchtitel);
					$handleurlObject = $this->urltypRepository->findByIdentifier($handleurltypUUID);
					$urlObject->setUrltyp($handleurlObject);
					$this->urlRepository->add($urlObject);
					$this->persistenceManager->persistAll();
					$handleurlUUID = $urlObject->getUUID();

					$bandhasurlObject = new Bandhasurl();
					$BandObject = $this->bandRepository->findByIdentifier($bandUUID);
					$bandhasurlObject->setBand($BandObject);
					$urlObject = $this->urlRepository->findByIdentifier($handleurlUUID);
					$bandhasurlObject->setUrl($urlObject);
					$this->bandHasUrlRepository->add($bandhasurlObject);
					$this->persistenceManager->persistAll();
				}
				if (isset($findpage) && !empty($findpage)) {
					$findpage = trim($findpage, "#");
					$findpage = explode("#", $findpage);
					$findpage = trim($findpage[0], "/");
					$urlObject = new Url();
					$urlObject->setUrl($findpage);
					$urlObject->setBemerkung($buchtitel);
					$findpageurlObject = $this->urltypRepository->findByIdentifier($findpageurltypUUID);
					$urlObject->setUrltyp($findpageurlObject);
					$this->urlRepository->add($urlObject);
					$this->persistenceManager->persistAll();
					$findpageurlUUID = $urlObject->getUUID();
					$bandhasurlObject = new Bandhasurl();
					$BandObject = $this->bandRepository->findByIdentifier($bandUUID);
					$bandhasurlObject->setBand($BandObject);
					$urlObject = $this->urlRepository->findByIdentifier($findpageurlUUID);
					$bandhasurlObject->setUrl($urlObject);
					$this->bandHasUrlRepository->add($bandhasurlObject);
					$this->persistenceManager->persistAll();
				}
				$nBand++;
			}
		}
		if ($this->logger) {
			$end = microtime(true);
			$time = number_format(($end - $start), 2);
			$this->logger->log('Band import completed in ' . round($time / 60, 2) . ' minutes.');
		}
		return $nBand;
	}

	/**
	 * Import Kloster table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_kloster
	 * @return int
	 */
	public function importKlosterAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Quelle"';
		$urltyp = $sqlConnection->fetchAll($sql);
		if (count($urltyp) > 0) {
			$urltypUUID = $urltyp[0]['persistence_object_identifier'];
		}
		if (!isset($urltypUUID)) {
			$urlTypeName = "Quelle";
			$urltypObject = new Urltyp();
			$urltypObject->setName($urlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$urltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "GND"';
		$gndurltyp = $sqlConnection->fetchAll($sql);
		if (count($gndurltyp) > 0) {
			$gndurltypUUID = $gndurltyp[0]['persistence_object_identifier'];
		}
		if (!isset($gndurltypUUID)) {
			$gndurlTypeName = "GND";
			$urltypObject = new Urltyp();
			$urltypObject->setName($gndurlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$gndurltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE name = "Wikipedia"';
		$wikiurltyp = $sqlConnection->fetchAll($sql);
		if (count($wikiurltyp) > 0) {
			$wikiurltypUUID = $wikiurltyp[0]['persistence_object_identifier'];
		}
		if (!isset($wikiurltypUUID)) {
			$wikiurlTypeName = "Wikipedia";
			$urltypObject = new Urltyp();
			$urltypObject->setName($wikiurlTypeName);
			$this->urltypRepository->add($urltypObject);
			$this->persistenceManager->persistAll();
			$wikiurltypUUID = $urltypObject->getUUID();
		}
		$sql = 'SELECT * FROM Kloster ORDER BY Klosternummer ASC';
		$klosters = $sqlConnection->fetchAll($sql);
		if (isset($klosters) and is_array($klosters)) {
			$nKloster = 0;
			foreach ($klosters as $key => $kloster) {
				$uid = $kloster['Klosternummer'];
				$numberOfKloster = count($this->klosterRepository->findOneByKloster_id($uid));
				if ($numberOfKloster == 0) {
					$wikipedia = $kloster['Wikipedia'];
					$gnd = $kloster['GND'];
					$hauptRessource = $kloster['HauptRessource'];
					$bearbeitungsstand = $kloster['Bearbeitungsstand'];
					$patrozinium = $kloster['Patrozinium'];
					$bemerkung = $kloster['Bemerkungen'];
					$creationdate = $kloster['Datensatz_angelegt'];
					$bearbeiter = trim($kloster['Bearbeiter']);
					$bearbeitungsstatus = trim($kloster['Status']);
					$personallistenstatus = trim($kloster['Personallisten']);
					if (!empty($bearbeiter)) {
						/** @var Bearbeiter $bearbeiterObject */
						$bearbeiterObject = $this->bearbeiterRepository->findOneByUid($bearbeiter);
						if (!empty($bearbeitungsstatus)) {
							/** @var Bearbeitungsstatus $bearbeitungsstatusObject */
							$bearbeitungsstatusObject = $this->bearbeitungsstatusRepository->findOneByName($bearbeitungsstatus);
							if (!empty($personallistenstatus)) {
								/** @var Personallistenstatus $personallistenstatusObject */
								$personallistenstatusObject = $this->personallistenstatusRepository->findOneByName($personallistenstatus);
								$band = $kloster['GermaniaSacraBandNr'];
								$band_seite = $kloster['GSBandSeite'];
								$text_gs_band = $kloster['TextGSBand'];
								$kloster = $kloster['Klostername'];
								$klosterObject = new Kloster();
								$klosterObject->setUid($uid);
								if (is_object($bearbeiterObject)) {
									$klosterObject->setBearbeiter($bearbeiterObject);
								}
								if (is_object($bearbeitungsstatusObject) AND $bearbeitungsstatusObject->getName() !== NULL) {
									$klosterObject->setBearbeitungsstatus($bearbeitungsstatusObject);
								} else {
									$result = $this->bearbeitungsstatusRepository->findLastEntry();
									if (count($result) === 1) {
										foreach ($result as $res) {
											$lastBearbeitungsstatusEntry = $res->getUid();
										}
										$bearbeitungsstatusUid = $lastBearbeitungsstatusEntry + 1;
									} else {
										$bearbeitungsstatusUid = 1;
									}
									$bearbeitungsstatusObject = new Bearbeitungsstatus();
									$bearbeitungsstatusObject->setUid($bearbeitungsstatusUid);
									$bearbeitungsstatusObject->setName($bearbeitungsstatus);
									$this->bearbeitungsstatusRepository->add($bearbeitungsstatusObject);
									$this->persistenceManager->persistAll();
									$bearbeitungsstatusUUID = $bearbeitungsstatusObject->getUUID();
									$bearbeitungsstatusObject = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatusUUID);
									$klosterObject->setBearbeitungsstatus($bearbeitungsstatusObject);
									$this->dumpImportlogger->log('Bearbeitungsstatus "' . $bearbeitungsstatus . '" fehlte in der Bearbeitungsstatustabelle. Er ist nun hinzugefügt. Kloster-uid: ' . $klosterObject->getUid(), LOG_INFO);
								}
								if (is_object($personallistenstatusObject)) {
									$klosterObject->setPersonallistenstatus($personallistenstatusObject);
								}
								$klosterObject->setKloster_id($uid);
								$klosterObject->setKloster($kloster);
								$klosterObject->setPatrozinium($patrozinium);
								$klosterObject->setBemerkung($bemerkung);
								if (null !== $band) {
									/** @var Band $bandObject */
									$bandObject = $this->bandRepository->findOneByUid($band);
									$klosterObject->setBand($bandObject);
									$klosterObject->setBand_seite($band_seite);
									$klosterObject->setText_gs_band($text_gs_band);
								}
								$klosterObject->setBearbeitungsstand($bearbeitungsstand);
								$klosterObject->setcreationDate(new \DateTime($creationdate));
								$this->klosterRepository->add($klosterObject);
								$this->persistenceManager->persistAll();
								$klosterUUID = $klosterObject->getUUID();
								if ($hauptRessource) {
									$parts = explode("#", $hauptRessource);
									if (count($parts) > 1) {
										$urlTypeName = "Quelle";
										if (!isset($urltypUUID)) {
											$urltypObject = new Urltyp();
											$urltypObject->setName($urlTypeName);
											$this->urltypRepository->add($urltypObject);
											$this->persistenceManager->persistAll();
											$urltypUUID = $urltypObject->getUUID();
										}
										foreach ($parts as $k => $value) {
											if (!($k % 2)) {
												if (!empty($value)) $urlBemerkung = $value;
											}
											if ($k % 2) {
												if (!empty($value)) $url = $value;
											}
											if ((isset($url) && !empty($url))) {
												$urlObject = new Url();
												$urlObject->setUrl($url);
												$urlObject->setBemerkung($urlBemerkung);
												/** @var UrlTyp $urltypObject */
												$urltypObject = $this->urltypRepository->findByIdentifier($urltypUUID);
												$urlObject->setUrltyp($urltypObject);
												$this->urlRepository->add($urlObject);
												$this->persistenceManager->persistAll();
												$urlUUID = $urlObject->getUUID();
												$klosterhasurlObject = new Klosterhasurl();
												/** @var Kloster $klosterObject */
												$klosterObject = $this->klosterRepository->findByIdentifier($klosterUUID);
												$klosterhasurlObject->setKloster($klosterObject);
												/** @var Url $urlObject */
												$urlObject = $this->urlRepository->findByIdentifier($urlUUID);
												$klosterhasurlObject->setUrl($urlObject);
												$this->klosterHasUrlRepository->add($klosterhasurlObject);
												$this->persistenceManager->persistAll();
											}
											if (isset($url)) {
												unset($url);
											}
										}
									}
								}
								if (isset($gnd) && !empty($gnd)) {
									$gnd = str_replace("\t", " ", $gnd);
									$gnd = str_replace("http:// ", " ", $gnd);
									$gnd = str_replace(" http", ";http", $gnd);
									$gnd = str_replace(";", "#", $gnd);
									$gnds = explode("#", $gnd);
									if (isset($gnds) && is_array($gnds)) {
										$oldgnd = "";
										foreach ($gnds as $gnd) {
											if (isset($gnd) && !empty($gnd)) {
												if ($gnd != $oldgnd) {
													$gnd = str_replace(" ", "", $gnd);
													$gnd = str_replace("# ", "", $gnd);
													$gndid = str_replace("http://d-nb.info/gnd/", "", $gnd);
													$gndbemerkung = $kloster . " [" . $gndid . "]";
													$urlObject = new Url();
													$urlObject->setUrl($gnd);
													$urlObject->setBemerkung($gndbemerkung);
													/** @var UrlTyp $gndurltypObject */
													$gndurltypObject = $this->urltypRepository->findByIdentifier($gndurltypUUID);
													$urlObject->setUrltyp($gndurltypObject);
													$this->urlRepository->add($urlObject);
													$this->persistenceManager->persistAll();
													$gndurlUUID = $urlObject->getUUID();
													$oldgnd = $gnd;
													$klosterhasurlObject = new Klosterhasurl();
													/** @var Kloster $klosterObject */
													$klosterObject = $this->klosterRepository->findByIdentifier($klosterUUID);
													$klosterhasurlObject->setKloster($klosterObject);
													/** @var Url $gndurlObject */
													$gndurlObject = $this->urlRepository->findByIdentifier($gndurlUUID);
													$klosterhasurlObject->setUrl($gndurlObject);
													$this->klosterHasUrlRepository->add($klosterhasurlObject);
													$this->persistenceManager->persistAll();
												}
											}
										}
									}
								}
								if (isset($wikipedia) && !empty($wikipedia)) {
									$wikipedia = str_replace("http:// ", " ", $wikipedia);
									$wikipedia = str_replace(";", "#", $wikipedia);
									$wikipedias = explode("#", $wikipedia);
									if (isset($wikipedias) && is_array($wikipedias)) {
										$oldwikipedia = "";
										foreach ($wikipedias as $wikipedia) {
											if (isset($wikipedia) && !empty($wikipedia)) {
												if ($wikipedia != $oldwikipedia) {
													$wikipediabemerkung = str_replace("http://de.wikipedia.org/wiki/", "", $wikipedia);
													$wikipediabemerkung = str_replace("_", " ", $wikipediabemerkung);
													$wikipediabemerkung = rawurldecode($wikipediabemerkung);
													$urlObject = new Url();
													$urlObject->setUrl($wikipedia);
													$urlObject->setBemerkung($wikipediabemerkung);
													$wikiurltypObject = $this->urltypRepository->findByIdentifier($wikiurltypUUID);
													$urlObject->setUrltyp($wikiurltypObject);
													$this->urlRepository->add($urlObject);
													$this->persistenceManager->persistAll();
													$wikiurlUUID = $urlObject->getUUID();
													$oldwikipedia = $wikipedia;
													$klosterhasurlObject = new Klosterhasurl();
													$klosterObject = $this->klosterRepository->findByIdentifier($klosterUUID);
													$klosterhasurlObject->setKloster($klosterObject);
													$wikiurlObject = $this->urlRepository->findByIdentifier($wikiurlUUID);
													$klosterhasurlObject->setUrl($wikiurlObject);
													$this->klosterHasUrlRepository->add($klosterhasurlObject);
													$this->persistenceManager->persistAll();
												}
											}
										}
									}
								}
							} else {
								$this->dumpImportlogger->log('Personallistenstatus zum Kloster ' . $uid . ' fehlt.', LOG_ERR);
							}
						} else {
							$this->logger->dumpImportlogger('Bearbeitungsstatus zum Kloster ' . $uid . ' fehlt.', LOG_ERR);
							if (empty($personallistenstatus)) {
								$this->logger->dumpImportlogger('Personallistenstatus zum Kloster ' . $uid . ' fehlt.', LOG_ERR);
							}
						}
					} else {
						$this->dumpImportlogger->log('Bearbeiter zum Kloster ' . $uid . ' fehlt.', LOG_ERR);
						if (empty($bearbeitungsstatus)) {
							$this->dumpImportlogger->log('Bearbeitungsstatus zum Kloster ' . $uid . ' fehlt.', LOG_ERR);
						}
						if (empty($personallistenstatus)) {
							$this->dumpImportlogger->log('Personallistenstatus zum Kloster ' . $uid . ' fehlt.', LOG_ERR);
						}
					}
					$nKloster++;
				} else {
					$this->dumpImportlogger->log('Doppelter Eintrag mit der Id = ' . $uid . ' in Klostertabelle. Der 2. Eintrag wurde ausgelassen.', LOG_ERR);
				}

				if (empty($uid)) {
					$this->dumpImportlogger->log('Das Kloster ' . $kloster . ' hat keine Klosternummer.', LOG_ERR);
				}
			}
			if ($this->logger) {
				$end = microtime(true);
				$time = number_format(($end - $start), 2);
				$this->logger->log('Kloster import completed in ' . round($time / 60, 2) . ' minutes.');
			}
			return $nKloster;
		}
	}

	/**
	 * Import Klosterstandort table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_klosterstandort
	 * @return int
	 */
	public function importKlosterstandortAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		$csvArr = $this->citekeysAction();
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT * FROM Klosterstandort ORDER  BY Klosternummer ASC';
		$Klosterstandorts = $sqlConnection->fetchAll($sql);
		$literaturKeyArr = array();
		if (isset($Klosterstandorts) and is_array($Klosterstandorts)) {
			$nKlosterstandort = 0;
			foreach ($Klosterstandorts as $Klosterstandort) {
				$uid = $Klosterstandort['ID_Kloster'];
				$kloster = $Klosterstandort['Klosternummer'];
				$ort = $Klosterstandort['ID_alleOrte'];
				$von_von = $Klosterstandort['Standort_von_von'];
				$von_bis = $Klosterstandort['Standort_Datum_von_bis'];
				$von_verbal = $Klosterstandort['Standort_von_Verbal'];
				$bis_von = $Klosterstandort['Standort_Datum_bis_von'];
				$bis_bis = $Klosterstandort['Standort_Datum_bis_bis'];
				$bis_verbal = $Klosterstandort['Standort_bis_Verbal'];
				$gruender = $Klosterstandort['Gruender'];
				$bemerkung = $Klosterstandort['interne_Anmerkungen'];
				$breite = $Klosterstandort['Breite'];
				$laenge = $Klosterstandort['Laenge'];
				if ($laenge > 180 || $laenge < -180) {
					$laenge = '';
				}
				if ($breite > 90 || $breite < -90) {
					$breite = '';
				}
				$bemerkung_standort = $Klosterstandort['BemerkungenStandort'];
				$temp_literatur_alt = $Klosterstandort['Literaturnachweise'];
				$lit = $temp_literatur_alt;
				$ortObject = $this->ortRepository->findOneByUid($ort);
				$klosterObject = $this->klosterRepository->findOneByUid($kloster);
				if ((is_object($klosterObject) && $klosterObject !== Null) && (is_object($ortObject) && $ortObject !== Null)) {
					$KlosterstandortObject = new Klosterstandort();
					$KlosterstandortObject->setUid($uid);
					if (isset($kloster) && !empty($kloster)) {
						$klosterObject = $this->klosterRepository->findOneByUid($kloster);
						$KlosterstandortObject->setKloster($klosterObject);
					}
					if (isset($ort) && !empty($ort)) {
						$ortObject = $this->ortRepository->findOneByUid($ort);
						$KlosterstandortObject->setOrt($ortObject);
					}
					$KlosterstandortObject->setVon_von($von_von);
					$KlosterstandortObject->setVon_bis($von_bis);
					$KlosterstandortObject->setVon_verbal($von_verbal);
					$KlosterstandortObject->setBis_von($bis_von);
					$KlosterstandortObject->setBis_bis($bis_bis);
					$KlosterstandortObject->setBis_verbal($bis_verbal);
					$KlosterstandortObject->setGruender($gruender);
					$KlosterstandortObject->setBemerkung($bemerkung);
					$KlosterstandortObject->setBreite($breite);
					$KlosterstandortObject->setLaenge($laenge);
					$KlosterstandortObject->setBemerkung_standort($bemerkung_standort);
					$KlosterstandortObject->setTemp_literatur_alt($temp_literatur_alt);
					$this->klosterstandortRepository->add($KlosterstandortObject);
					$this->persistenceManager->persistAll();
					if (isset($lit) && !empty($lit)) {
						$lit = trim($lit, "- −");
						$lit = str_replace(" − ", " - ", $lit);
						$lit = str_replace(" — ", " - ", $lit);
						$lit = str_replace("\r\n", " - ", $lit);
						$lit = str_replace("—", " - ", $lit);
						$lit = str_replace(" – ", " - ", $lit);
						$lit = str_replace(", S[^.]", ", S.", $lit);
						$lit = str_replace(",S.", ", S.", $lit);
						$lits = explode(" - ", $lit);
						foreach ($lits as $key => $litItem) {
							$parts = trim($litItem);
							$parts = explode(', S.', $parts);
							$buch = trim($parts[0]);
							$buch = utf8_decode($buch);
							$seite = "";
							if (count($parts) > 1) {
								$seite = 'S. ' . trim($parts[1], ' .');
							}
							$beschreibung = $seite;
							if (array_key_exists($buch, $csvArr)) {
								$citekey = $csvArr[$buch]['citekey'];
								if (!empty($citekey)) {
									if ($citekey and $csvArr[$buch]['detail'] and $csvArr[$buch]['detail'] != '#N/A') {
										if ($beschreibung and !strpos($csvArr[$buch]['detail'], $beschreibung)) {
											$beschreibung = $csvArr[$buch]['detail'] . ', ' . $beschreibung;
										} else {
											$beschreibung = $csvArr[$buch]['detail'];
										}
									}
									$literaturKey = $uid . "-" . $citekey . "-" . utf8_decode($beschreibung);
									if (!in_array($literaturKey, $literaturKeyArr)) {
										array_push($literaturKeyArr, $literaturKey);
										$literaturObject = new Literatur();
										$literaturObject->setCitekey($citekey);
										$literaturObject->setBeschreibung($beschreibung);
										$this->literaturRepository->add($literaturObject);
										$this->persistenceManager->persistAll();
										$literaturUUID = $literaturObject->getUUID();
										$klosterhasliteraturObject = new KlosterHasLiteratur();
										$klosterhasliteraturObject->setKloster($klosterObject);
										$literaturObject = $this->literaturRepository->findByIdentifier($literaturUUID);
										$klosterhasliteraturObject->setLiteratur($literaturObject);
										$this->klosterHasLiteraturRepository->add($klosterhasliteraturObject);
										$this->persistenceManager->persistAll();
									}
								} else {
									$this->dumpImportlogger->log('Kein citekey für das Buch ' . $buch . ' beim Kloster mit der Id = ' . $kloster . ' vorhanden.', LOG_ERR);
								}
							} else {
								$this->dumpImportlogger->log('Entweder keine Literatur oder keine Übereinstimmung für das Kloster mit der Id = ' . $kloster . ' vorhanden.', LOG_ERR);
								$this->dumpImportlogger->log('Der Buchtitel lautet: ' . utf8_encode($buch), LOG_INFO);
							}
						}
					}
					$nKlosterstandort++;
				} else {
					if ($klosterObject === Null) {
						$this->dumpImportlogger->log('Entweder ist das Feld Klosternummer in Klosterstandorttabelle leer oder das Klosterobject in der Klostertabelle für das Kloster mit der Id = ' . $kloster . ' wurde nicht gefunden.', LOG_ERR);
					}
					if ($ortObject === Null) {
						$this->dumpImportlogger->log('Entweder ist das Feld ID_alleOrte in Klosterstandorttabelle leer oder das Ortobject in der Orttabelle für den Ort mit der Id = ' . $ort . ' wurde nicht gefunden.', LOG_ERR);
					}
				}
			}
			if ($this->logger) {
				$end = microtime(true);
				$time = number_format(($end - $start), 2);
				$this->logger->log('Klosterstandort import completed in ' . round($time / 60, 2) . ' minutes.');
			}
			return $nKlosterstandort;
		}
	}

	/**
	 * Import Orden table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_orden
	 * @return int
	 */
	public function importOrdenAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT * FROM Orden';
		$ordens = $sqlConnection->fetchAll($sql);
		$ordenstypArr = array();
		if (isset($ordens) and is_array($ordens)) {
			$nOrden = 0;
			foreach ($ordens as $ordenvalue) {
				$uid = $ordenvalue['ID_Ordo'];
				$orden = $ordenvalue['Ordensbezeichnung'];
				$ordo = $ordenvalue['Ordo'];
				$symbol = $ordenvalue['Symbol'];
				$graphik = null;
				if (isset($ordenvalue['Grafikdatei']) && !empty($ordenvalue['Grafikdatei'])) {
					$graphik = $ordenvalue['Grafikdatei'];
				}
				$ordenstyp = $ordenvalue['Geschlecht'];
				$gnd = $ordenvalue['GND_Orden'];
				$wikipedia = $ordenvalue['Wikipedia_Orden'];
				if (empty($ordenstyp)) {
					$ordenstyp = 'unbekannt';
				}
				if (!in_array($ordenstyp, $ordenstypArr)) {
					$ordenstypObject = new Ordenstyp();
					$ordenstypObject->setOrdenstyp($ordenstyp);
					$this->ordenstypRepository->add($ordenstypObject);
					$this->persistenceManager->persistAll();
					$ordenstypUUID = $ordenstypObject->getUUID();
				}
				array_push($ordenstypArr, $ordenstyp);
				if (isset($ordenstypUUID) && !empty($ordenstypUUID)) {
					$ordenstypObject = $this->ordenstypRepository->findByIdentifier($ordenstypUUID);
				} else {
					$ordenstypObject = $this->ordenstypRepository->findOneByOrdenstyp($ordenstyp);
				}
				$ordenObject = new Orden();
				$ordenObject->setUid($uid);
				$ordenObject->setOrden($orden);
				$ordenObject->setOrdo($ordo);
				$ordenObject->setSymbol($symbol);
				$ordenObject->setGraphik($graphik);
				$ordenObject->setOrdenstyp($ordenstypObject);
				$this->ordenRepository->add($ordenObject);
				$this->persistenceManager->persistAll();
				unset($ordenstypUUID);
				$ordenUUID = $ordenObject->getUUID();
				if (isset($gnd) && !empty($gnd)) {
					$gnd = str_replace("\t", " ", $gnd);
					$gnd = str_replace("http:// ", " ", $gnd);
					$gnd = str_replace(" http", ";http", $gnd);
					$gnd = str_replace(";", "#", $gnd);
					$gnds = explode("#", $gnd);
					if (isset($gnds) && is_array($gnds)) {
						$oldgnd = "";
						foreach ($gnds as $gnd) {
							if (isset($gnd) && !empty($gnd)) {
								if ($gnd != $oldgnd) {
									$gnd = str_replace(" ", "", $gnd);
									$gnd = str_replace("# ", "", $gnd);
									$gndid = str_replace("http://d-nb.info/gnd/", "", $gnd);
									$gndbemerkung = $orden . " [" . $gndid . "]";
									$urlObject = new Url();
									$urlObject->setUrl($gnd);
									$urlObject->setBemerkung($gndbemerkung);
									$gndurltypObject = $this->urltypRepository->findOneByName('GND');
									$urlObject->setUrltyp($gndurltypObject);
									$this->urlRepository->add($urlObject);
									$this->persistenceManager->persistAll();
									$gndurlUUID = $urlObject->getUUID();
									$oldgnd = $gnd;
									$ordenhasurlObject = new Ordenhasurl();
									$ordenObject = $this->ordenRepository->findByIdentifier($ordenUUID);
									$ordenhasurlObject->setOrden($ordenObject);
									$gndurlObject = $this->urlRepository->findByIdentifier($gndurlUUID);
									$ordenhasurlObject->setUrl($gndurlObject);
									$this->ordenHasUrlRepository->add($ordenhasurlObject);
									$this->persistenceManager->persistAll();
								}
							}
						}
					}
				}
				if (isset($wikipedia) && !empty($wikipedia)) {
					$wikipedia = str_replace("http:// ", " ", $wikipedia);
					$wikipedia = str_replace(";", "#", $wikipedia);
					$wikipedias = explode("#", $wikipedia);
					if (isset($wikipedias) && is_array($wikipedias)) {
						$oldwikipedia = "";
						foreach ($wikipedias as $wikipedia) {
							if (isset($wikipedia) && !empty($wikipedia)) {
								if ($wikipedia != $oldwikipedia) {
									$wikipediabemerkung = str_replace("http://de.wikipedia.org/wiki/", "", $wikipedia);
									$wikipediabemerkung = str_replace("_", " ", $wikipediabemerkung);
									$wikipediabemerkung = rawurldecode($wikipediabemerkung);
									$urlObject = new Url();
									$urlObject->setUrl($wikipedia);
									$urlObject->setBemerkung($wikipediabemerkung);
									$wikiurltypObject = $this->urltypRepository->findOneByName('Wikipedia');
									$urlObject->setUrltyp($wikiurltypObject);
									$this->urlRepository->add($urlObject);
									$this->persistenceManager->persistAll();
									$wikiurlUUID = $urlObject->getUUID();
									$oldwikipedia = $wikipedia;
									$ordenhasurlObject = new Ordenhasurl();
									$ordenObject = $this->ordenRepository->findByIdentifier($ordenUUID);
									$ordenhasurlObject->setOrden($ordenObject);
									$wikiurlObject = $this->urlRepository->findByIdentifier($wikiurlUUID);
									$ordenhasurlObject->setUrl($wikiurlObject);
									$this->ordenHasUrlRepository->add($ordenhasurlObject);
									$this->persistenceManager->persistAll();
								}
							}
						}
					}
				}
				$nOrden++;
			}
			if ($this->logger) {
				$end = microtime(true);
				$time = number_format(($end - $start), 2);
				$this->logger->log('Orden import completed in ' . round($time / 60, 2) . ' minutes.');
			}
			return $nOrden;
		}
	}

	/**
	 * Import Klosterorden table into the FLOW domain_model tabel subugoe_germaniasacra_domain_klosterorden_band
	 * @return int
	 */
	public function importKlosterordenAction() {
		if ($this->logger) {
			$start = microtime(true);
		}
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT * FROM Klosterorden ORDER BY ID_KlosterOrden ASC';
		$klosterordens = $sqlConnection->fetchAll($sql);
		$klosterstatusArr = array();
		if (isset($klosterordens) and is_array($klosterordens)) {
			$nKlosterorden = 0;
			foreach ($klosterordens as $klosterorden) {
				$uid = $klosterorden['ID_KlosterOrden'];
				$kloster = $klosterorden['Klosternummer'];
				$orden = $klosterorden['Orden'];
				if ((isset($kloster) && !empty($kloster)) && (isset($orden) && !empty($orden))) {
					$klosterObject = $this->klosterRepository->findOneByUid($kloster);
					$ordenObject = $this->ordenRepository->findOneByUid($orden);
					if ((is_object($klosterObject) && $klosterObject !== Null) && (is_object($ordenObject) && $ordenObject !== Null)) {
						if (!isset($klosterorden['Klosterstatus']) || empty($klosterorden['Klosterstatus'])) {
							$klosterorden['Klosterstatus'] = "keine Angabe";
						}
						$klosterstatus = $klosterorden['Klosterstatus'];
						if (!in_array($klosterstatus, $klosterstatusArr)) {
							array_push($klosterstatusArr, $klosterstatus);
							$klosterstatusObject = new Klosterstatus();
							$klosterstatusObject->setStatus($klosterstatus);
							$this->klosterstatusRepository->add($klosterstatusObject);
							$this->persistenceManager->persistAll();
							$klosterstatusUUID = $klosterstatusObject->getUUID();
							$klosterstatusObject = $this->klosterstatusRepository->findByIdentifier($klosterstatusUUID);
						} else {
							$klosterstatusObject = $this->klosterstatusRepository->findOneByStatus($klosterstatus);
						}
						$von_von = $klosterorden['von_von'];
						$von_bis = $klosterorden['von_bis'];
						$von_verbal = $klosterorden['verbal_von'];
						$bis_von = $klosterorden['bis_von'];
						$bis_bis = $klosterorden['bis_bis'];
						$bis_verbal = $klosterorden['verbal_bis'];
						$bemerkung = $klosterorden['interne_Anmerkungen'];
						$klosterordenObject = new Klosterorden();
						$klosterordenObject->setUid($uid);
						$klosterordenObject->setKloster($klosterObject);
						$klosterordenObject->setOrden($ordenObject);
						$klosterordenObject->setKlosterstatus($klosterstatusObject);
						$klosterordenObject->setVon_von($von_von);
						$klosterordenObject->setVon_bis($von_bis);
						$klosterordenObject->setVon_verbal($von_verbal);
						$klosterordenObject->setBis_von($bis_von);
						$klosterordenObject->setBis_bis($bis_bis);
						$klosterordenObject->setBis_verbal($bis_verbal);
						$klosterordenObject->setBemerkung($bemerkung);
						$this->klosterordenRepository->add($klosterordenObject);
						$this->persistenceManager->persistAll();
					}
					$nKlosterorden++;
				} else {
					if ($klosterObject === Null) {
						$this->dumpImportlogger->log('Entweder ist das Feld Klosternummer in Klosterordentabelle leer oder das Klosterobject in der Klostertabelle für das Kloster mit der Id = ' . $kloster . ' wurde nicht gefunden.', LOG_ERR);
					}

					if ($ordenObject === Null) {
						$this->dumpImportlogger->log('Entweder ist das Feld Orden in Klosterordentabelle leer oder das Ordenobject in der Ordentabelle für den Orden mit der Id = ' . $orden . ' wurde nicht gefunden.', LOG_ERR);
					}
				}
			}
			if ($this->logger) {
				$end = microtime(true);
				$time = number_format(($end - $start), 2);
				$this->logger->log('Klosterorden import completed in ' . round($time / 60, 2) . ' minutes.');
			}
			return $nKlosterorden;
		}
	}

	/**
	 * Process GS-citekeys.csv file and return an array for further Literatur processing
	 * @return array $csvArr The array created from csv file
	 */
	public function citekeysAction() {
		$file = "GS-citekeys.csv";
		if (!file_exists($this->dumpDirectory . $file)) {
			throw new \TYPO3\Flow\Resource\Exception('File ' . $file . ' not present in ' . $this->dumpDirectory, 1398846324);
		}
		$csvArr = array();
		$csv = array_map('str_getcsv', file($this->dumpDirectory . $file));
		foreach ($csv as $key => $value) {
			if ($key > 0) {
				if (isset($value[1]) && !empty($value[1])) {
					$titel = $value[1];
					$titel = utf8_decode($titel);
				}
				if (isset($value[2]) && !empty($value[2])) {
					$citekey = $value[2];
				} else $citekey = null;
				if (isset($value[3]) && !empty($value[3])) {
					$detail = $value[3];
				} else $detail = null;
				if (isset($titel) && !empty($titel)) {
					$csvArr[$titel] = array('title' => $titel, 'citekey' => $citekey, 'detail' => $detail);
				}
			}
		}

		return $csvArr;
	}

	/**
	 * Process and import access SQL dump data into the corresponding flow tables
	 * @return void
	 */
	public function access2mysqlAction() {
		$this->initializeLogger();
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET unique_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$this->importDumpFromGithubAction();
		$this->delAccessTabsAction();
		$this->importAccessAction();
		$this->emptyTabsAction();
		$this->dumpImportlogger->log('########## Folgende Datensätze wurden importiert am ' . date('d.m.Y H:i:s') . ' ##########');
		$nBearbeiter = $this->importBearbeiterAction();
		$this->dumpImportlogger->log($nBearbeiter . ' Bearbeiter Datensätze');
		$this->importPersonallistenstatusAction();
		$nLand = $this->importLandAction();
		$this->dumpImportlogger->log($nLand . ' Land Datensätze');
		$nOrt = $this->importOrtAction();
		$this->dumpImportlogger->log($nOrt . ' Ort Datensätze');
		$nBistum = $this->importBistumAction();
		$this->dumpImportlogger->log($nBistum . ' Bistum Datensätze');
		$nBand = $this->importBandAction();
		$this->dumpImportlogger->log($nBand . ' Band Datensätze');
		$nKloster = $this->importKlosterAction();
		$this->dumpImportlogger->log($nKloster . ' Kloster Datensätze');
		$nKlosterstandort = $this->importKlosterstandortAction();
		$this->dumpImportlogger->log($nKlosterstandort . ' Klosterstandort Datensätze');
		$nOrden = $this->importOrdenAction();
		$this->dumpImportlogger->log($nOrden . ' Orden Datensätze');
		$nKlosterorden = $this->importKlosterordenAction();
		$this->dumpImportlogger->log($nKlosterorden . ' Klosterorden Datensätze');
		$this->delAccessTabsAction();
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * Drop all imported access tables
	 * @return void
	 */
	public function delAccessTabsAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'Band, Bearbeiter, Bistum, Kloster, Klosterstandort, Land, Ort, Orden, Klosterorden';
		$sql = 'DROP TABLE IF EXISTS  ' . $tbl;
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * Truncate bearbeitungsstatus table of Germania Sacra package
	 * @return void
	 */
	public function emptyBearbeitungsstatusTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bearbeitungsstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate bearbeiter table of Germania Sacra package
	 * @return void
	 */
	public function emptyBearbeiterTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bearbeiter';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate personallistenstatus table of Germania Sacra package
	 * @return void
	 */
	public function emptyPersonallistenstatusTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_personallistenstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate land table of Germania Sacra package
	 * @return void
	 */
	public function emptyLandTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_land';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate ort table of Germania Sacra package
	 * @return void
	 */
	public function emptyOrtTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate orthasurl table of Germania Sacra package
	 * @return void
	 */
	public function emptyOrtHasUrlTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_orthasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate bistum table of Germania Sacra package
	 * @return void
	 */
	public function emptyBistumTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bistum';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate bistumhasurl table of Germania Sacra package
	 * @return void
	 */
	public function emptyBistumHasUrlTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bistumhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate band table of Germania Sacra package
	 * @return void
	 */
	public function emptyBandTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_band';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate bandhasurl table of Germania Sacra package
	 * @return void
	 */
	public function emptyBandHasUrlTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bandhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate urltyp table of Germania Sacra package
	 * @return void
	 */
	public function emptyUrltypTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate kloster table of Germania Sacra package
	 * @return void
	 */
	public function emptyKlosterTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_kloster';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * Truncate url table of Germania Sacra package
	 * @return void
	 */
	public function emptyUrlTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_url';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate klosterhasurl table of Germania Sacra package
	 * @return void
	 */
	public function emptyKlosterHasUrlTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate klosterstandort table of Germania Sacra package
	 * @return void
	 */
	public function emptyKlosterstandortTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterstandort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate bibitem table of Germania Sacra package
	 * @return void
	 */
	public function emptyBibitemTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bibitem';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate literatur table of Germania Sacra package
	 * @return void
	 */
	public function emptyLiteraturTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_literatur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate klosterhasliteratur table of Germania Sacra package
	 * @return void
	 */
	public function emptyKlosterHasLiteraturTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasliteratur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate orden table of Germania Sacra package
	 * @return void
	 */
	public function emptyOrdenTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_orden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate ordenstyp table of Germania Sacra package
	 * @return void
	 */
	public function emptyOrdenstypTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_ordenstyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate ordenhasurl table of Germania Sacra package
	 * @return void
	 */
	public function emptyOrdenHasUrlTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_ordenhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate klosterorden table of Germania Sacra package
	 * @return void
	 */
	public function emptyKlosterordenTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterorden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate klosterstatus table of Germania Sacra package
	 * @return void
	 */
	public function emptyKlosterstatusTabAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$this->logger->log("Die Tabelle " . $tbl . " wurde entleert.");
		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
		exit;
	}

	/**
	 * Truncate all the available Germania Sacra package tables
	 * @return void
	 */
	public function emptyTabsAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_bearbeitungsstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_personallistenstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_land';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_orthasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bistum';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bistumhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_band';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bandhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_kloster';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_url';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterstandort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bibitem';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_literatur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasliteratur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_orden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_ordenstyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_ordenhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterorden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * Import access SQL dump into Flow
	 * @return void
	 * @throws \Exception
	 */
	public function importAccessAction() {
		$logger = new \TYPO3\Flow\Log\Logger();
		$dumpFileName = 'klosterdatenbankdump.sql';
		if (!is_dir($this->dumpDirectory)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($this->dumpDirectory);
		}
		if (!file_exists($this->dumpDirectory . $dumpFileName)) {
			throw new \TYPO3\Flow\Resource\Exception(1398846324);
		}
		$sql = file_get_contents($this->dumpDirectory . $dumpFileName);
		$sql = str_replace('CREATE DATABASE IF NOT EXISTS `Klosterdatenbank`;', '', $sql);
		$sql = str_replace('USE `Klosterdatenbank`;', '', $sql);
		$sql = str_replace('Bearbeitet von', 'Bearbeiter', $sql);
		$sql = str_replace('tblBundesländer', 'Land', $sql);
		$sql = str_replace('tblalleOrte', 'Ort', $sql);
		$sql = str_replace('tblBistum', 'Bistum', $sql);
		$sql = str_replace('tblGSBaende', 'Band', $sql);
		$sql = str_replace('tblKlosterStammblatt', 'Kloster', $sql);
		$sql = str_replace('tblKlosterStandort', 'Klosterstandort', $sql);
		$sql = str_replace('tblOrden', 'Orden', $sql);
		$sql = str_replace('tblKlosterOrden', 'Klosterorden', $sql);
		$sql = str_replace('`Wüstung`', '`Wuestung`', $sql);
		$sql = str_replace('`Datensatz angelegt`', '`Datensatz_angelegt`', $sql);
		$sql = str_replace('`Ordenszugehörigkeit`', '`Orden`', $sql);
		$sql = str_replace('`Ordenszugehörigkeit_von_von`', '`von_von`', $sql);
		$sql = str_replace('`Ordenszugehörigkeitvon__bis`', '`von_bis`', $sql);
		$sql = str_replace('`OrdenszugehörigkeitVerbal_von`', '`verbal_von`', $sql);
		$sql = str_replace('`Ordenszugehörigkeit_bis_von`', '`bis_von`', $sql);
		$sql = str_replace('`Ordenzugehörigkeit_bis_bis`', '`bis_bis`', $sql);
		$sql = str_replace('`OrdenszugehörigkeitVerbal_bis`', '`verbal_bis`', $sql);
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * Import incremental kloster dump from Github
	 * @return boolean
	 * @throws \Exception
	 */
	public function importDumpFromGithubAction() {
		if (!is_dir($this->dumpDirectory)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($this->dumpDirectory);
		}
		if (is_file($this->accessDumpFilenamePath)) {
			if (!unlink($this->accessDumpFilenamePath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t unlink the access dump file.', 1406721004);
			}
		}
		if (is_file($this->citekeysFilenamePath)) {
			if (!unlink($this->citekeysFilenamePath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t unlink the citekeys file.', 1406721013);
			}
		}
		if (!is_file($this->cacertFilenamePath)) {
			if (!is_dir(self::cacertDest)) {
				\TYPO3\Flow\Utility\Files::createDirectoryRecursively(self::cacertDest);
			}
			if (!copy($this->cacertSourcePath, $this->cacertDestPath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t copy the cacert file.', 1406721027);
			}
		}
		$this->client->authenticate($this->settings['git']['token'], $password = '', $this->method);
		$accessDumpHash = $this->getFileHashAction($this->client, self::accessDumpFilename);
		$accessDumpBlob = $this->client->api('git_data')->blobs()->show(self::githubUser, self::githubRepository, $accessDumpHash);
		$accessDumpBlob = base64_decode($accessDumpBlob['content']);
		$citekeysHash = $this->getFileHashAction($this->client, self::citekeysFilename);
		$citekeysBlob = $this->client->api('git_data')->blobs()->show(self::githubUser, self::githubRepository, $citekeysHash);
		$citekeysBlob = base64_decode($citekeysBlob['content']);
		$mode = 'w';
		$fp = fopen($this->accessDumpFilenamePath, $mode);
		if (!$fp) {
			throw new \TYPO3\Flow\Resource\Exception('Can\'t create the access dump file.', 1406721039);
		} else {
			fwrite($fp, $accessDumpBlob);
			fclose($fp);
		}
		$fp = fopen($this->citekeysFilenamePath, $mode);
		if (!$fp) {
			throw new \TYPO3\Flow\Resource\Exception('Can\'t create the citekeys csv file.', 1406721048);
		} else {
			fwrite($fp, $citekeysBlob);
			fclose($fp);
		}
		return true;
	}

	/**
	 * Import incremental kloster data from within FLOW into db kloster table
	 * @return void
	 * @throws \Exception
	 */
	public function importInkDumpAction() {
		if (!is_dir($this->dumpDirectory)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($this->dumpDirectory);
		}
		if (is_file($this->inkKlosterDumpFilenamePath)) {
			if (!unlink($this->inkKlosterDumpFilenamePath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t unlink the incremental kloster dump file.', 1406721073);
			}
		}

		if (!is_file($this->cacertFilenamePath)) {
			if (!is_dir(FLOW_PATH_ROOT . self::cacertDest)) {
				\TYPO3\Flow\Utility\Files::createDirectoryRecursively(FLOW_PATH_ROOT. self::cacertDest);
			}
			if (!copy($this->cacertSourcePath, $this->cacertDestPath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t copy the cacert file.', 1406721027);
			}
		}

		$this->client->authenticate($this->settings['git']['token'], $password = '', $this->method);
		$inkKlosterDumpHash = $this->getFileHashAction($this->client, self::inkKlosterDumpFilename);
		$inkKlosterDumpBlob = $this->client->api('git_data')->blobs()->show(self::githubUser, self::githubRepository, $inkKlosterDumpHash);
		$inkKlosterDumpBlob = base64_decode($inkKlosterDumpBlob['content']);

		$mode = 'w';
		$fp = fopen($this->inkKlosterDumpFilenamePath, $mode);
		if (!$fp) {
			throw new \TYPO3\Flow\Resource\Exception('Can\'t create the incremental kloster dump file.', 1406721082);
		} else {
			fwrite($fp, $inkKlosterDumpBlob);
			fclose($fp);
		}
		$this->delAccessTabsAction();
		$this->importAccessInkDumpAction();
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$this->initializeLogger();
		$this->dumpImportlogger->log('########## Folgende Datensätze wurden importiert am ' . date('d.m.Y H:i:s') . ' ##########');
		$checkIfBearbeiterTableExists = $sqlConnection->getSchemaManager()->tablesExist('Bearbeiter');
		if ($checkIfBearbeiterTableExists) {
			$nBearbeiter = $this->importBearbeiterAction();
			$this->dumpImportlogger->log($nBearbeiter . ' Bearbeiter Datensätze');
		}
		$checkIfLandTableExists = $sqlConnection->getSchemaManager()->tablesExist('Land');
		if ($checkIfLandTableExists) {
			$nLand = $this->importLandAction();
			$this->dumpImportlogger->log($nLand . ' Land Datensätze');
		}
		$checkIfOrtTableExists = $sqlConnection->getSchemaManager()->tablesExist('Ort');
		if ($checkIfOrtTableExists) {
			$nOrt = $this->importOrtAction();
			$this->dumpImportlogger->log($nOrt . ' Ort Datensätze');
		}
		$checkIfBistumTableExists = $sqlConnection->getSchemaManager()->tablesExist('Bistum');
		if ($checkIfBistumTableExists) {
			$nBistum = $this->importBistumAction();
			$this->dumpImportlogger->log($nBistum . ' Bistum Datensätze');
		}
		$checkIfBandTableExists = $sqlConnection->getSchemaManager()->tablesExist('Band');
		if ($checkIfBandTableExists) {
			$nBand = $this->importBandAction();
			$this->dumpImportlogger->log($nBand . ' Band Datensätze');
		}
		$checkIfKlosterTableExists = $sqlConnection->getSchemaManager()->tablesExist('Kloster');
		if ($checkIfKlosterTableExists) {
			$nKloster = $this->importKlosterAction();
			$this->dumpImportlogger->log($nKloster . ' Kloster Datensätze');
		}
		$checkIfKlosterstandortTableExists = $sqlConnection->getSchemaManager()->tablesExist('Klosterstandort');
		if ($checkIfKlosterstandortTableExists) {
			$nKlosterstandort = $this->importKlosterstandortAction();
			$this->dumpImportlogger->log($nKlosterstandort . ' Klosterstandort Datensätze');
		}
		$checkIfOrdenTableExists = $sqlConnection->getSchemaManager()->tablesExist('Orden');
		if ($checkIfOrdenTableExists) {
			$nOrden = $this->importOrdenAction();
			$this->dumpImportlogger->log($nOrden . ' Orden Datensätze');
		}
		$checkIfKlosterordenTableExists = $sqlConnection->getSchemaManager()->tablesExist('Klosterorden');
		if ($checkIfKlosterordenTableExists) {
			$nKlosterorden = $this->importKlosterordenAction();
			$this->dumpImportlogger->log($nKlosterorden . ' Klosterorden Datensätze');
		}
		$this->delAccessTabsAction();
		if ($this->cacheInterface->has('getOptions')) {
			$this->cacheInterface->remove('getOptions');
		}
		$this->redirect('list', 'Kloster', 'Subugoe.GermaniaSacra');
	}

	/**
	 * Import access incremental kloster data dump
	 * @return void
	 * @throws \Exception
	 */
	public function importAccessInkDumpAction() {
		if (!is_dir($this->dumpDirectory)) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($this->dumpDirectory);
		}
		if (!file_exists($this->inkKlosterDumpFilenamePath)) {
			throw new \TYPO3\Flow\Resource\Exception(1398846324);
		}
		$sql = file_get_contents($this->inkKlosterDumpFilenamePath);
		$sql = str_replace('CREATE DATABASE IF NOT EXISTS `Klosterdatenbank`;', '', $sql);
		$sql = str_replace('USE `Klosterdatenbank`;', '', $sql);
		$sql = str_replace('Bearbeitet von', 'Bearbeiter', $sql);
		$sql = str_replace('tblBundesländer', 'Land', $sql);
		$sql = str_replace('tblalleOrte', 'Ort', $sql);
		$sql = str_replace('tblBistum', 'Bistum', $sql);
		$sql = str_replace('tblGSBaende', 'Band', $sql);
		$sql = str_replace('tblKlosterStammblatt', 'Kloster', $sql);
		$sql = str_replace('tblKlosterStandort', 'Klosterstandort', $sql);
		$sql = str_replace('tblOrden', 'Orden', $sql);
		$sql = str_replace('tblKlosterOrden', 'Klosterorden', $sql);
		$sql = str_replace('`Wüstung`', '`Wuestung`', $sql);
		$sql = str_replace('`Datensatz angelegt`', '`Datensatz_angelegt`', $sql);
		$sql = str_replace('`Ordenszugehörigkeit`', '`Orden`', $sql);
		$sql = str_replace('`Ordenszugehörigkeit_von_von`', '`von_von`', $sql);
		$sql = str_replace('`Ordenszugehörigkeitvon__bis`', '`von_bis`', $sql);
		$sql = str_replace('`OrdenszugehörigkeitVerbal_von`', '`verbal_von`', $sql);
		$sql = str_replace('`Ordenszugehörigkeit_bis_von`', '`bis_von`', $sql);
		$sql = str_replace('`Ordenzugehörigkeit_bis_bis`', '`bis_bis`', $sql);
		$sql = str_replace('`OrdenszugehörigkeitVerbal_bis`', '`verbal_bis`', $sql);
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * get file hash from Github for getting the corresponding blob
	 * @param object $client
	 * @param string $filename
	 * @return string $dumpHash
	 */
	private function getFileHashAction($client, $filename) {
		$refs = $client->api('git_data')->references()->branches(self::githubUser, self::githubRepository);
		$masterSha = $refs[0]['object']['sha'];
		$trees = $this->client->api('git_data')->trees()->show(self::githubUser, self::githubRepository, $masterSha);

		foreach ($trees['tree'] as $k => $tree) {
			if ($tree['path'] == $filename) {
				$dumpHash = $tree['sha'];
			}
		}

		return $dumpHash;
	}

	/**
	 * @param string $fullName
	 * @return string
	 */
	protected function createUsername($fullName) {
		$username = implode('.', explode(' ', $fullName));
		$username = strtolower(str_replace(
				array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'),
				array('Ae', 'ae', 'Oe', 'oe', 'Ue', 'ue', 'ss'), $username));

		return $username;
	}

	/**
	 * @return string
	 */
	private function createPassword() {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_.$@#&0123456789";
		$password = '';
		for ($i = 0; $i < 8; $i++) {
			$password .= substr($chars, rand(0, strlen($chars) - 1), 1);
		}
		return $password;
	}

	/**
	 * @param string $userName
	 * @param string $password
	 */
	private function createUsernamePasswordFile($userName, $password, $uid) {
		if ($uid == 1) {
			$usernamePassword = '########## New username-password list dated ' . date('d.m.Y H:i:s') . ' ##########' . PHP_EOL;
		} else {
			$usernamePassword = '';
		}
		$usernamePassword .= 'Benutzername: ' . $userName . PHP_EOL;
		$usernamePassword .= 'Password: ' . $password . PHP_EOL;
		$usernamePassword .= PHP_EOL;
		$usernamePasswordFile = FLOW_PATH_DATA . 'Persistent/GermaniaSacra/Data/usernamePassword.txt';
		if (!is_dir(dirname($usernamePasswordFile))) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively(dirname($usernamePasswordFile));
		}
		file_put_contents($usernamePasswordFile, $usernamePassword);
	}

	protected function initializeLogger() {
		$log = new LoggerFactory();
		if (file_exists(FLOW_PATH_DATA . self::dumpLogFile)) {
			unlink(FLOW_PATH_DATA . self::dumpLogFile);
		}
		$this->dumpImportlogger = $log->create('GermaniaSacra',
				'TYPO3\Flow\Log\Logger',
				'\TYPO3\Flow\Log\Backend\FileBackend',
				array(
						'logFileUrl' => FLOW_PATH_DATA . self::dumpLogFile,
						'createParentDirectories' => TRUE
				)
		);
	}

}
