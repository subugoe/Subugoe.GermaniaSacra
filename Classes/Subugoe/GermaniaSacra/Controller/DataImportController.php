<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
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
use Subugoe\GermaniaSacra\Domain\Model\Bibitem;
use Subugoe\GermaniaSacra\Domain\Model\Literatur;
use Subugoe\GermaniaSacra\Domain\Model\Orden;
use Subugoe\GermaniaSacra\Domain\Model\Klosterstandort;
use Subugoe\GermaniaSacra\Domain\Model\Klosterorden;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur;
use Subugoe\GermaniaSacra\Domain\Model\OrdenHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\BandHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl;
use Subugoe\GermaniaSacra\Domain\Model\OrtHasUrl;

class DataImportController extends ActionController {

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BibitemRepository
	 */
	protected $bibitemRepository;

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\ZeitraumRepository
	 */
	protected $zeitraumRepository;

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
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

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
	 * @var \Gitonomy\Git\Repository
	 */
	protected $Repository;

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

	const accessDumpFilename = 'klosterdatenbankdump.sql';

	const citekeysFilename = 'GS-citekeys.csv';

	const cacertFilename = 'cacert.pem';

	const inkKlosterDumpFilename = 'inkKlosterDump.sql';

	const cacertSource = 'Packages/Libraries/guzzle/guzzle/src/Guzzle/Http/Resources/cacert.pem';

	const cacertDest = 'Data/GermaniaSacra/Dump/cacert.pem';

	const  githubUser = 'subugoe';

	const githubRepository = 'GermaniaSacra-dumps';

	public function __construct($logger = NULL, $settings = NULL) {
		parent::__construct();
		$this->dumpDirectory = FLOW_PATH_ROOT . 'Data/GermaniaSacra/Dump';
		$this->accessDumpFilenamePath = FLOW_PATH_ROOT . 'Data/GermaniaSacra/Dump/' . self::accessDumpFilename;
		$this->citekeysFilenamePath = FLOW_PATH_ROOT . 'Data/GermaniaSacra/Dump/' . self::citekeysFilename;
		$this->inkKlosterDumpFilenamePath = FLOW_PATH_ROOT . 'Data/GermaniaSacra/Dump/' . self::inkKlosterDumpFilename;
		$this->cacertFilenamePath = FLOW_PATH_ROOT . 'Data/GermaniaSacra/Dump/' . self::cacertFilename;
		$this->cacertSourcePath = FLOW_PATH_ROOT . self::cacertSource;
		$this->cacertDestPath = FLOW_PATH_ROOT . self::cacertDest;
		$this->logger = $logger;
		$this->settings = $settings;
		$this->client = new \Github\Client();
		$this->method = \Github\Client::AUTH_URL_TOKEN;

		if (!$this->logger) {
					$log = new \TYPO3\Flow\Log\LoggerFactory();

					$this->logger = $log->create(
							'GermaniaSacra',
							'TYPO3\Flow\Log\Logger',
							'\TYPO3\Flow\Log\Backend\FileBackend',
							array(
									'logFileUrl' => FLOW_PATH_DATA . 'GermaniaSacra/Log/inkKlosterDump.log',
									'createParentDirectories' => TRUE
							)
					);
				}
	}

	/**
	 * Imports Bearbeitungsstatus table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_bearbeitungsstatus
	 * @return void
	 */
	public function importBearbeitungsstatusAction() {
		$bearbeitungsstatusArr = array(1 => 'Angaben unklar',
				2 => 'Daten importiert',
				3 => 'Quellenlage unvollständig',
				4 => 'Geprüft (bei Eingabe)',
				5 => 'Redaktionell geprüft',
				6 => 'Neuaufnahme, unvollständig',
				7 => 'Online',
				8 => 'Dublette BW'
		);
		if (isset($bearbeitungsstatusArr) and is_array($bearbeitungsstatusArr)) {
			foreach ($bearbeitungsstatusArr as $key => $name) {
				$bearbeitungsstatusObject = new Bearbeitungsstatus();
				$bearbeitungsstatusObject->setUid($key);
				$bearbeitungsstatusObject->setName($name);
				$this->bearbeitungsstatusRepository->add($bearbeitungsstatusObject);
				$this->persistenceManager->persistAll();
			}
		}
	}

	/**
	 * Imports Bearbeiter table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_bearbeiter
	 * @return void
	 */
	public function importBearbeiterAction() {
		/** @var \Doctrine\DBAL\Connection $sqlConnection */
		$sqlConnection = $this->entityManager->getConnection();

		$checkIfTableExists = $sqlConnection->getSchemaManager()->tablesExist('subugoe_germaniasacra_domain_model_bearbeiter');
		if (!$checkIfTableExists) {

			$sql = 'SELECT ID, Bearbeiter FROM Bearbeiter';
			$bearbeiters = $sqlConnection->fetchAll($sql);
			if (isset($bearbeiters) and is_array($bearbeiters)) {
				foreach ($bearbeiters as $be) {
					$uid = $be['ID'];
					$bearbeiter = $be['Bearbeiter'];

					$userName = $this->createUsername($bearbeiter);
					$password = $this->createPassword();

					$account = $this->accountFactory->createAccountWithPassword($userName,$password, array('Flow.Login:Administrator'));
					$this->accountRepository->add($account);
					$bearbeiterObject = new Bearbeiter();
					$bearbeiterObject->setUid($uid);
					$bearbeiterObject->setBearbeiter($bearbeiter);
					$bearbeiterObject->setAccount($account);
					$this->bearbeiterRepository->add($bearbeiterObject);
					$this->persistenceManager->persistAll();
					$this->createUsernamePasswordFile($userName, $password);
				}
			}
		}
	}

	/**
	 * Imports Personallistenstatus table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_personallistenstatus
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
	 * Imports Länder table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_land
	 * @return void
	 */
	public function importLandAction() {
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT ID_Bundesland, Land, Deutschland FROM Land';
		$Lands = $sqlConnection->fetchAll($sql);
		if (isset($Lands) and is_array($Lands)) {
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
			}
		}
	}

	/**
	 * Imports Orte table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_ort
	 * @return void
	 */
	public function importOrtAction() {
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = "ANALYZE LOCAL TABLE " . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sqlConnection->close();

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
			}
		}
	}

	/**
	 * Imports Bistums table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_bistum
	 * @return void
	 */
	public function importBistumAction() {
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
				$GNDLabel = '';
				if ($is_erzbistum)
					$GNDLabel = 'Erzbistum';
				else
					$GNDLabel = 'Bistum';
				$GNDLabel .= ' ' . $bistum;
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
			}
		}

		// Added to prevent wrong search result
		$ortBistum = $this->bistumRepository->findOneByBistum('keine Angabe');
		$ortTbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = 'SELECT * FROM ' . $ortTbl . ' WHERE bistum IS Null';
		$orts = $sqlConnection->fetchAll($sql);
		if (!empty($orts)) {
			foreach ($orts as $ort) {
				$ortObject = $this->ortRepository->findOneByUid($ort['uid']);
				$ortObject->setBistum($ortBistum);
				$this->ortRepository->update($ortObject);
				$this->persistenceManager->persistAll();
			}
		}

	}

	/**
	 * Imports Bände table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_band
	 * @return void
	 */
	public function importBandAction() {
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
			}
		}

		// This is added to prevent wrong search result
		$uid = $Band['ID_GSBand'] + 1;
		$nummer = 'keine Angabe';
		$sortierung = $Band['Sortierung'] + 1;
		$titel = 'keine Angabe';
		$kurztitel = 'keine Angabe';
		$bandObject = new Band();
		$bandObject->setUid($uid);
		$bandObject->setNummer($nummer);
		$bandObject->setSortierung($sortierung);
		$bandObject->setTitel($titel);
		$bandObject->setKurztitel($kurztitel);
		$this->bandRepository->add($bandObject);
		$this->persistenceManager->persistAll();

	}

	/**
	 * Imports Klöster table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_kloster
	 * @return void
	 */
	public function importKlosterAction() {
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

		// Added to prevent wrong search result
		$defaultUrlTypeName = "keine Angabe";
		$defaultUrltypObject = new Urltyp();
		$defaultUrltypObject->setName($defaultUrlTypeName);
		$this->urltypRepository->add($defaultUrltypObject);
		$this->persistenceManager->persistAll();


		$defaultUrl = "keine Angabe";
		$defaultUrlObject = new Url();
		$defaultUrlObject->setUrl($defaultUrl);
		$defaultUrlObject->setUrltyp($defaultUrltypObject);
		$this->urlRepository->add($defaultUrlObject);
		$this->persistenceManager->persistAll();


		$sql = 'SELECT * FROM Kloster ORDER BY Klosternummer ASC';
		$klosters = $sqlConnection->fetchAll($sql);
		if (isset($klosters) and is_array($klosters)) {
			foreach ($klosters as $key => $kloster) {
				$wikipedia = $kloster['Wikipedia'];
				$gnd = $kloster['GND'];
				$hauptRessource = $kloster['HauptRessource'];
				$bearbeitungsstand = $kloster['Bearbeitungsstand'];
				$patrozinium = $kloster['Patrozinium'];
				$bemerkung = $kloster['Bemerkungen'];
				$creationdate = $kloster['Datensatz_angelegt'];
				$uid = $kloster['Klosternummer'];
				$bearbeiter = $kloster['Bearbeiter'];
				$bearbeitungsstatus = $kloster['Status'];
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
								$kloster_id = $kloster['Klosternummer'];
								$kloster = $kloster['Klostername'];
								$klosterObject = new Kloster();
								$klosterObject->setUid($uid);
								if (is_object($bearbeiterObject)) {
									$klosterObject->setBearbeiter($bearbeiterObject);
								}
								if (is_object($bearbeitungsstatusObject) AND $bearbeitungsstatusObject->getName() !== NULL) {
									$klosterObject->setBearbeitungsstatus($bearbeitungsstatusObject);
								} else {
									$lastBearbeitungsstatusEntry = $this->bearbeitungsstatusRepository->findLastEntry();
									$bearbeitungsstatusUid = $lastBearbeitungsstatusEntry['uid'] + 1;
									$bearbeitungsstatusObject = new Bearbeitungsstatus();
									$bearbeitungsstatusObject->setUid($bearbeitungsstatusUid);
									$bearbeitungsstatusObject->setName($bearbeitungsstatus);
									$this->bearbeitungsstatusRepository->add($bearbeitungsstatusObject);
									$this->persistenceManager->persistAll();
									$bearbeitungsstatusUUID = $bearbeitungsstatusObject->getUUID();
									$bearbeitungsstatusObject = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatusUUID);
									$klosterObject->setBearbeitungsstatus($bearbeitungsstatusObject);
									$this->logger->log('Bearbeitungsstatus "' . $bearbeitungsstatus . '" was missing in Bearbeitungsstatus table. It is added now. Kloster entry: ' . $klosterObject->getUid());

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
								}
								// Added to prevent wrong search result
								else {
									$this->bandRepository->setDefaultOrderings(
											array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING)
									);
									$bandObject = $this->bandRepository->findAll()->getFirst();
									$klosterObject->setBand($bandObject);
								}
								$klosterObject->setBand_seite($band_seite);
								$klosterObject->setText_gs_band($text_gs_band);
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
										}
										$urlObject = new Url();
										$urlObject->setUrl($parts[1]);
										$urlObject->setBemerkung($parts[0]);
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
							}
							else {
								echo 'Personallistenstatus zum Kloster ' . $uid . 'fehlt.<br>';
								$this->logger->log('Personallistenstatus zum Kloster ' . $uid . " fehlt.", LOG_INFO);
							}
						}
						else {
							echo 'Bearbeitunsstatus zum Kloster ' . $uid . 'fehlt.<br>';
							$this->logger->log('Bearbeitungsstatus zum Kloster ' . $uid . " fehlt.", LOG_INFO);
							if (empty($personallistenstatus)) {
								echo 'Personallistenstatus zum Kloster ' . $uid . 'fehlt.<br>';
								$this->logger->log('Personallistenstatus zum Kloster ' . $uid . " fehlt.", LOG_INFO);
							}
						}
					}
				else {
					echo 'Bearbeiter zum Kloster ' . $uid . ' fehlt.<br>';
					$this->logger->log('Bearbeiter zum Kloster ' . $uid . " fehlt.", LOG_INFO);
					if (empty($bearbeitungsstatus)) {
						echo 'Bearbeitunsstatus zum Kloster ' . $uid . ' fehlt.<br>';
						$this->logger->log('Bearbeitungsstatus zum Kloster ' . $uid . " fehlt.", LOG_INFO);
					}
					if (empty($personallistenstatus)) {
						echo 'Personallistenstatus zum Kloster ' . $uid . ' fehlt.<br>';
						$this->logger->log('Personallistenstatus zum Kloster ' . $uid . " fehlt.", LOG_INFO);
					}
				}
			}
		}
	}

	/**
	* Adds a default URL for to prevent wrong search result
	* @return void
	*/
	public function addDefaultUrlAction() {
		$defaultUrlTypeName = "keine Angabe";
		$defaultUrltypObject = new Urltyp();
		$defaultUrltypObject->setName($defaultUrlTypeName);
		$this->urltypRepository->add($defaultUrltypObject);
		$this->persistenceManager->persistAll();
		$defaultUrl = "keine Angabe";
		$defaultUrlObject = new Url();
		$defaultUrlObject->setUrl($defaultUrl);
		$defaultUrlObject->setUrltyp($defaultUrltypObject);
		$this->urlRepository->add($defaultUrlObject);
		$this->persistenceManager->persistAll();
		$klosters = $this->klosterRepository->findAll();
		foreach ($klosters as $kloster) {
			$urls = $kloster->getKlosterHasUrls();
			if (count($urls) == 0) {
				$klosterHasUrlObject = new KlosterHasUrl();
				$klosterHasUrlObject->setKloster($kloster);
				$klosterHasUrlObject->setUrl($defaultUrlObject);
				$this->klosterHasUrlRepository->add($klosterHasUrlObject);
				$this->persistenceManager->persistAll();
			}
		}
	}

	/**
	 * Imports Klosterstandorte table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_klosterstandort
	 * @return void
	 */
	public function importKlosterstandortAction() {
		$csvArr = $this->citekeysAction();
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT * FROM Klosterstandort ORDER  BY ID_Kloster ASC';
		$Klosterstandorts = $sqlConnection->fetchAll($sql);
		$buecher = array();
		$literaturKeyArr = array();
		if (isset($Klosterstandorts) and is_array($Klosterstandorts)) {
			foreach ($Klosterstandorts as $Klosterstandort) {
				$uid = $Klosterstandort['ID_Kloster'];
				$klosterObject = $this->klosterRepository->findOneByUid($uid);
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
						if (isset($buch) && !empty($buch) && !in_array($buch, $buecher)) {
							array_push($buecher, $buch);
							$bibitemObject = new Bibitem();
							$bibitemObject->setBibitem($buch);
							$this->bibitemRepository->add($bibitemObject);
							$this->persistenceManager->persistAll();
							$bibiitemUid = $bibitemObject->getUid();
						}
						$beschreibung = $seite;
						if (array_key_exists($buch, $csvArr)) {
							$citekey = $csvArr[$buch]['citekey'];
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
						}
					}
				}
			}
		}
	}

	/**
	 * Imports Orden table into the FLOW domain_model tabel subugoe_germaniasacra_domain_model_orden
	 * @return void
	 */
	public function importOrdenAction() {
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT * FROM Orden';
		$ordens = $sqlConnection->fetchAll($sql);
		$ordenstypArr = array();
		if (isset($ordens) and is_array($ordens)) {
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
			}
		}
	}

	/**
	 * Imports Klosterorden table into the FLOW domain_model tabel subugoe_germaniasacra_domain_klosterorden_band
	 * @return void
	 */
	public function importKlosterordenAction() {
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SELECT * FROM Klosterorden ORDER BY ID_KlosterOrden ASC';
		$klosterordens = $sqlConnection->fetchAll($sql);
		$klosterstatusArr = array();
		if (isset($klosterordens) and is_array($klosterordens)) {
			foreach ($klosterordens as $klosterorden) {
				$uid = $klosterorden['ID_KlosterOrden'];
				$kloster = $klosterorden['Klosternummer'];
				$orden = $klosterorden['Orden'];
				if ((isset($kloster) && !empty($kloster)) && (isset($orden) && !empty($orden))) {
					$klosterObject = $this->klosterRepository->findOneByUid($kloster);
					$ordenObject = $this->ordenRepository->findOneByUid($orden);
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
			}
		}
	}

	/**
	 * Process GS-citekeys.csv file and return an array for further Literatur processing
	 * @return array $csvArr The array created from csv file
	 */
	public function citekeysAction() {
		$file = "GS-citekeys.csv";
		if (!file_exists($this->dumpDirectory . '/' . $file)) {
			throw new \TYPO3\Flow\Resource\Exception('File ' . $file . ' not present in ' . $this->dumpDirectory, 1398846324);
		}
		$csvArr = array();
		$csv = array_map('str_getcsv', file($this->dumpDirectory . '/' . $file));
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
	 */
	public function access2mysqlAction() {

		$log = new \TYPO3\Flow\Log\LoggerFactory();
		$this->logger = $log->create(
				'GermaniaSacra',
				'TYPO3\Flow\Log\Logger',
				'\TYPO3\Flow\Log\Backend\FileBackend',
				array(
						'logFileUrl' => FLOW_PATH_DATA . 'Logs/GermaniaSacra/AccessImport.log',
						'createParentDirectories' => TRUE
				)
		);

		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET unique_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);

		$this->delAccessTabsAction();
		$this->importAccessAction();
		$this->emptyTabsAction();
		$this->importBearbeitungsstatusAction();
		$this->importBearbeiterAction();
		$this->importPersonallistenstatusAction();
		$this->importLandAction();
		$this->importOrtAction();
		$this->importBistumAction();
		$this->importBandAction();
		$this->importKlosterAction();
		$this->importKlosterstandortAction();
		$this->importOrdenAction();
		$this->importKlosterordenAction();
		$this->delAccessTabsAction();

		$sql = 'SET foreign_key_checks = 1';
		$sqlConnection->executeUpdate($sql);
	}

	public function delAccessTabsAction() {
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bearbeitungsstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bearbeiter';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_personallistenstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_land';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_orthasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bistum';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bistumhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_band';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bandhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_kloster';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_url';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterstandort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_bibitem';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_literatur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasliteratur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_orden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_ordenstyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_ordenhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterorden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$sql = 'SET foreign_key_checks = 0';
		$sqlConnection->executeUpdate($sql);
		$tbl = 'subugoe_germaniasacra_domain_model_klosterstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
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
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'subugoe_germaniasacra_domain_model_bearbeitungsstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bearbeiter';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_personallistenstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_land';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_ort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_orthasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bistum';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bistumhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_band';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bandhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_urltyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_kloster';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_url';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterstandort';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_bibitem';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_literatur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterhasliteratur';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_orden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_ordenstyp';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_ordenhasurl';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterorden';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);

		$tbl = 'subugoe_germaniasacra_domain_model_klosterstatus';
		$sql = 'DELETE FROM ' . $tbl;
		$sqlConnection->executeUpdate($sql);
		$sql = 'ALTER TABLE ' . $tbl . ' AUTO_INCREMENT = 1';
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * import access SQL dump into Flow
	 * @return void
	 */
	public function importAccessAction() {
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
		$sqlConnection = $this->entityManager->getConnection();
		$sqlConnection->executeUpdate($sql);
	}

	public function importDumpFromGithubAction() {

		if (!is_dir($this->dumpDirectory)) {
			if (!mkdir($this->dumpDirectory, 0755)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t create the dump directory.', 1406720986);
			}
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
			if (!copy($this->cacertSourcePath, $this->cacertDestPath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t copy the cacert file.', 1406721027);
			}
		}

		$this->client->authenticate($this->settings['git']['token'], $password='', $this->method);
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
		}
		else {
			fwrite ($fp, $accessDumpBlob);
			fclose($fp);
		}

		$fp = fopen($this->citekeysFilenamePath, $mode);
		if (!$fp) {
			throw new \TYPO3\Flow\Resource\Exception('Can\'t create the citekeys csv file.', 1406721048);
		}
		else {
			fwrite ($fp, $citekeysBlob);
			fclose($fp);
		}

		return true;
	}

	/**
	 * import incremental kloster data from within FLOW into db kloster table
	 * @return void
	 */
	public function importInkKlosterDataDumpAction() {
		$this->client->authenticate($this->settings['git']['token'], $password='', $this->method);
		$inkKlosterDumpHash = $this->getFileHashAction($this->client, self::inkKlosterDumpFilename);
		$inkKlosterDumpBlob = $this->client->api('git_data')->blobs()->show(self::githubUser, self::githubRepository, $inkKlosterDumpHash);
		$inkKlosterDumpBlob = base64_decode($inkKlosterDumpBlob['content']);
		if (!is_dir($this->dumpDirectory)) {
			if (!mkdir($this->dumpDirectory, 0755)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t create the dump directory.', 1406721061);
			}
		}
		if (is_file($this->inkKlosterDumpFilenamePath)) {
			if (!unlink($this->inkKlosterDumpFilenamePath)) {
				throw new \TYPO3\Flow\Resource\Exception('Can\'t unlink the incremental kloster dump file.', 1406721073);
			}
		}

		$mode = 'w';
		$fp = fopen($this->inkKlosterDumpFilenamePath, $mode);
		if (!$fp) {
			throw new \TYPO3\Flow\Resource\Exception('Can\'t create the incremental kloster dump file.', 1406721082);
		}
		else {
			fwrite ($fp, $inkKlosterDumpBlob);
			fclose($fp);
		}

		$this->delAccessKlosterTabAction();
		$this->importAccessInkKlosterDataAction();
		$this->importKlosterAction();
		$this->delAccessKlosterTabAction();

		echo "Inkrementelle Kloster-Dump wurde importiert.";
		exit;

	}

	/**
	 * import access incremental kloster data dump
	 * @return void
	 */
	public function importAccessInkKlosterDataAction() {
		$logger = new \TYPO3\Flow\Log\Logger();
		$dumpFileName = 'inkKlosterDump.sql';
		if (!is_dir($this->dumpDirectory)) {
			throw new \TYPO3\Flow\Resource\Exception;
		}
		if (!file_exists($this->dumpDirectory . '/' . $dumpFileName)) {
			throw new \TYPO3\Flow\Resource\Exception(1398846324);
		}
		$sql = file_get_contents($this->dumpDirectory . '/' . $dumpFileName);
		$sql = str_replace('tblKlosterStammblatt', 'Kloster', $sql);
		$sql = str_replace('`Datensatz angelegt`', '`Datensatz_angelegt`', $sql);
		$sqlConnection = $this->entityManager->getConnection();
		$sqlConnection->executeUpdate($sql);
	}

	public function delAccessKlosterTabAction() {
		$sqlConnection = $this->entityManager->getConnection();
		$tbl = 'Kloster';
		$sql = 'DROP TABLE IF EXISTS  ' . $tbl;
		$sqlConnection->executeUpdate($sql);
	}

	/**
	 * get file hash from Github for getting the corresponding blob
	 * @return string $inkKlosterDumHash
	 */

	private function getFileHashAction($client, $filename) {
		$refs = $client->api('git_data')->references()->branches(self::githubUser, self::githubRepository);
		$masterSha = $refs[0]['object']['sha'];
		$trees = $this->client->api('git_data')->trees()->show(self::githubUser, self::githubRepository, $masterSha);

		foreach ($trees['tree'] as $k => $tree) {
			if ($tree['path'] == $filename) {
				$inkKlosterDumHash = $tree['sha'];
			}
		}

		return $inkKlosterDumHash;

	}

	/**
	 * @param string $fullName
	 * @return string
	 */
	protected function createUsername($fullName) {
		$username = implode('.', explode(' ', $fullName));
		$username = strtolower(str_replace(
		array('Ä','ä','Ö','ö','Ü','ü','ß'),
		array('Ae','ae','Oe','oe','Ue','ue','ss'), $username));

		return $username;
	}

	/**
	 * @return string
	 */
	private function createPassword() {
		$chars ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_.$@#&0123456789";
		$password = '';
		for($i = 0; $i < 8; $i++) {
			$password .= substr($chars, rand(0, strlen($chars) - 1), 1);
		}
		return $password;
	}

	/**
	 * @param string $userName
	 * @param string $password
	 */
	private function createUsernamePasswordFile($userName, $password) {
		$usernamePassword = 'Benutzername: ' . $userName . PHP_EOL;
		$usernamePassword .= 'Password: ' . $password . PHP_EOL;
		$usernamePassword .= PHP_EOL;

		$usernamePasswordFile = FLOW_PATH_DATA . 'GermaniaSacra/Data/usernamePassword.txt';
		if (!is_dir(dirname($usernamePasswordFile))) {
			mkdir(dirname($usernamePasswordFile), 0777, TRUE);
		}
		file_put_contents($usernamePasswordFile, $usernamePassword,  FILE_APPEND);
	}
}
?>
