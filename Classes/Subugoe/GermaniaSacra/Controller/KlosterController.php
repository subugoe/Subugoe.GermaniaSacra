<?php
namespace Subugoe\GermaniaSacra\Controller;


use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Kloster;
use Subugoe\GermaniaSacra\Domain\Model\Klosterstandort;
use Subugoe\GermaniaSacra\Domain\Model\Klosterorden;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur;
use Subugoe\GermaniaSacra\Domain\Model\Url;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl;

class KlosterController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BandRepository
	 */
	protected $bandRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LiteraturRepository
	 */
	protected $literaturRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumRepository
	 */
	protected $bistumRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenRepository
	 */
	protected $ordenRepository;

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
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterHasUrlRepository
	 */
	protected $klosterHasUrlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\UrlRepository
	 */
	protected $urlRepository;

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\UrltypRepository
	 */
	protected $urltypRepository;

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

	/** Updates the list of Kloster
	 * @FLOW\SkipCsrfProtection
	 * @return integer $status http status
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('auswahl')) {
			$auswahlArr = $this->request->getArgument('auswahl');
		}

		if ($this->request->hasArgument('bearbeitungsstatus')) {
			$bearbeitungsstatusArr = $this->request->getArgument('bearbeitungsstatus');
		}

		if ($this->request->hasArgument('kloster')) {
			$klosterArr = $this->request->getArgument('kloster');
		}

		if ($this->request->hasArgument('ort')) {
			$ortArr = $this->request->getArgument('ort');
		}

		if ($this->request->hasArgument('gnd')) {
			$gndArr = $this->request->getArgument('gnd');
		}

		if ($this->request->hasArgument('bearbeitungsstand')) {
			$bearbeitungsstandArr = $this->request->getArgument('bearbeitungsstand');
		}

		$list = array();
		foreach ($auswahlArr as $auswahl) {
			if (
					(isset($klosterArr[$auswahl]) && !empty($klosterArr[$auswahl])) &&
					(isset($bearbeitungsstatusArr[$auswahl]) && !empty($bearbeitungsstatusArr[$auswahl]))
			) {
				$list[$auswahl] = array("bearbeitungsstatus" => $bearbeitungsstatusArr[$auswahl][0],
						"klostername" => $klosterArr[$auswahl][0],
						"gnd" => $gndArr[$auswahl][0],
						"bearbeitungsstand" => $bearbeitungsstandArr[$auswahl][0],
						"ort" => $ortArr[$auswahl]);
			}
		}

		if (isset($list) && !empty($list)) {
			foreach ($list as $k => $v) {
				$klosterObject = $this->klosterRepository->findByIdentifier($k);
				$klosterObject->setKloster($v['klostername']);
				$bearbeitungsstatusObject = $this->bearbeitungsstatusRepository->findByIdentifier($v['bearbeitungsstatus']);
				$klosterObject->setBearbeitungsstatus($bearbeitungsstatusObject);
				$klosterObject->setBearbeitungsstand($v['bearbeitungsstand']);
				$this->klosterRepository->update($klosterObject);

				$klosterHasUrls = $klosterObject->getKlosterHasUrls();
				foreach ($klosterHasUrls as $klosterHasUrl) {
					$urlObject = $klosterHasUrl->getUrl();
					$urlTypObject = $urlObject->getUrltyp();
					$urlTyp = $urlTypObject->getName();
					if ($urlTyp == "GND") {
						$urlObject->setUrl($v['gnd']);
						$this->urlRepository->update($urlObject);
						$this->persistenceManager->persistAll();
					}
				}

				$klosterstandorts = $klosterObject->getKlosterstandorts();
				if (is_object($klosterstandorts)) {
					foreach ($klosterstandorts as $i => $klosterstandort) {
						$this->klosterstandortRepository->remove($klosterstandort);
					}
				}

				$ort = $v['ort'];
				if (isset($ort) && !empty($ort) && is_array($ort)) {
					foreach ($ort as $ort_uuid) {
						$ortObject = $this->ortRepository->findByIdentifier($ort_uuid);
						if (is_object($ortObject)) {
							$klosterstandort = new Klosterstandort();
							$klosterstandort->setKloster($klosterObject);
							$klosterstandort->setOrt($ortObject);
							$this->klosterstandortRepository->add($klosterstandort);
						}
					}
				}
			}
			$this->persistenceManager->persistAll();
		}

		$status = 200;
		return json_encode(array($status));
	}

	/**
	 * Fetches all monasteries and assigns them as json to the view
	 */
	public function listAction() {
		if ($this->request->getFormat() === 'json') {
			$this->view->setVariablesToRender(array('monasteries'));
		}
		$this->view->assign('monasteries', $this->klosterRepository->findAll());
	}

	/**
	 * @return array $reponse The list (10 entries each time) of Kloster in json format
	 */
	public function jsonListAction() {
		if ($this->request->hasArgument('page')) {
			$page = $this->request->getArgument('page');
		} else $page = 1;

		$offset = ($page - 1) * 10;
		$limit = 10;

		$this->klosterRepository->setDefaultOrderings(
				array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING)
		);

		$klosters = $this->klosterRepository->findKlosters($offset, $limit);

		$klosterArr = array();
		foreach ($klosters as $k => $kloster) {
			$klosterArr[$k]['uuid'] = $kloster->getUUID();
			$klosterArr[$k]['uid'] = $kloster->getUid();
			$klosterArr[$k]['kloster'] = $kloster->getKloster();
			$klosterArr[$k]['kloster_id'] = $kloster->getKloster_id();
			$klosterArr[$k]['bearbeitungsstand'] = $kloster->getBearbeitungsstand();
			$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
			$klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus->getUUID();

			$klosterstandorts = $kloster->getKlosterstandorts();
			foreach ($klosterstandorts as $i => $klosterstandort) {
				$ort = $klosterstandort->getOrt();
				$klosterArr[$k]['ort'][$i] = $ort->getOrt();
			}

			$klosterHasUrls = $kloster->getKlosterHasUrls();
			foreach ($klosterHasUrls as $klosterHasUrl) {
				$urlObj = $klosterHasUrl->getUrl();
				$url = $urlObj->getUrl();
				$urlTypObj = $urlObj->getUrltyp();
				$urlTyp = $urlTypObj->getName();
				if ($urlTyp == "GND") {
					$klosterArr[$k]['gnd'] = $url;
				}
			}
		}

		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n => $bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		$response = array();
		$response[] = $klosterArr;
		$response[] = $bearbeitungsstatusArr;

		return json_encode($response);
	}

	/**
	 * @return array $reponse The list of all Kloster in json format
	 */
	public function klosterListAllAction() {

		$this->klosterRepository->setDefaultOrderings(
				array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING)
		);

		$klosters = $this->klosterRepository->findAll();

		$klosterArr = array();
		foreach ($klosters as $k => $kloster) {
			$klosterArr[$k]['uuid'] = $kloster->getUUID();
			$klosterArr[$k]['kloster'] = $kloster->getKloster();
			$klosterArr[$k]['kloster_id'] = $kloster->getKloster_id();
			$klosterArr[$k]['bearbeitungsstand'] = $kloster->getBearbeitungsstand();
			$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
			$klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus->getUUID();

			$klosterstandorts = $kloster->getKlosterstandorts();
			foreach ($klosterstandorts as $i => $klosterstandort) {
				$ort = $klosterstandort->getOrt();
				if (is_object($ort)) {
					$klosterArr[$k]['ort'][$i] = $ort->getOrt();
				}
			}

			$klosterHasUrls = $kloster->getKlosterHasUrls();
			foreach ($klosterHasUrls as $i => $klosterHasUrl) {
				$urlObj = $klosterHasUrl->getUrl();
				$url = $urlObj->getUrl();
				$urlTypObj = $urlObj->getUrltyp();
				$urlTyp = $urlTypObj->getName();
				if ($urlTyp == "GND") {
					$klosterArr[$k]['gnd'] = $url;
				}
			}
		}

		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n => $bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		$response = array();
		$response[] = $klosterArr;
		$response[] = $bearbeitungsstatusArr;

		return json_encode($response);
	}

	/**
	 * Calls the index page
	 * @return void
	 */
	public function indexAction() {
		if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
			$query_string = explode("=", $_SERVER["QUERY_STRING"]);
			$page = (integer)trim($query_string[1]);
		}
		if (!isset($page) && empty($page)) $page = 1;
		$this->view->assign('klosters', $this->klosterRepository->findAll());
		$this->view->assign('page', $page);
	}

	/**
	 * @return array $response The data needed for select boxes in json format
	 */
	public function newAction() {
		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n => $bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		$personallistenstatusArr = array();
		$personallistenstatuses = $this->personallistenstatusRepository->findAll();
		foreach ($personallistenstatuses as $m => $personallistenstatus) {
			$personallistenstatusArr[$m] = array($personallistenstatus->getName() => $personallistenstatus->getUUID());
		}

		$bandArr = array();
		$this->bandRepository->setDefaultOrderings(
				array('titel' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bands = $this->bandRepository->findAll();
		foreach ($bands as $p => $band) {
			$bandArr[$p] = array($band->getTitel() => $band->getUUID());
		}

		$literaturArr = array();
		$this->literaturRepository->setDefaultOrderings(
				array('citekey' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$literaturs = $this->literaturRepository->findAll();
		foreach ($literaturs as $q => $literatur) {
			$literaturArr[$q] = array($literatur->getCitekey() => $literatur->getUUID());
		}

		$bistumArr = array();
		$this->bistumRepository->setDefaultOrderings(
				array('bistum' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bistums = $this->bistumRepository->findAll();
		foreach ($bistums as $r => $bistum) {
			$bistumArr[$r] = array($bistum->getBistum() => $bistum->getUUID());
		}

		$ordenArr = array();
		$this->ordenRepository->setDefaultOrderings(
				array('orden' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$ordens = $this->ordenRepository->findAll();
		foreach ($ordens as $m => $orden) {
			$ordenArr[$m] = array($orden->getOrden() => $orden->getUUID());
		}

		$klosterstatusArr = array();
		$this->klosterstatusRepository->setDefaultOrderings(
				array('status' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$klosterstatuses = $this->klosterstatusRepository->findAll();
		foreach ($klosterstatuses as $n => $klosterstatus) {
			$klosterstatusArr[$n] = array($klosterstatus->getStatus() => $klosterstatus->getUUID());
		}

		$bearbeiterArr = array();
		$this->bearbeiterRepository->setDefaultOrderings(
				array('bearbeiter' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bearbeiters = $this->bearbeiterRepository->findAll();
		foreach ($bearbeiters as $q => $bearbeiter) {
			$bearbeiterArr[$q] = array($bearbeiter->getBearbeiter() => $bearbeiter->getUUID());
		}

		$response = array();
		$response[] = $bearbeitungsstatusArr;
		$response[] = $personallistenstatusArr;
		$response[] = $bandArr;
		$response[] = $literaturArr;
		$response[] = $bistumArr;
		$response[] = $ordenArr;
		$response[] = $klosterstatusArr;
		$response[] = $bearbeiterArr;

		return json_encode($response);
	}

	/**
	 * Create a new Kloster with attached Klosterstandort/Klosterorden/Klosterliteratur/Klosterurl
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function createAction() {

		$kloster = new Kloster();
		$kloster->setUid($this->getLastKlosterIdAction());

		// Add Kloster
		$kloster->setKloster( $this->request->getArgument('kloster_name') );
		$kloster->setPatrozinium( $this->request->getArgument('patrozinium') );
		$kloster->setBemerkung( $this->request->getArgument('bemerkung') );
		$kloster->setBand_seite( $this->request->getArgument('band_seite') );
		$kloster->setText_gs_band( $this->request->getArgument('text_gs_band') );
		$kloster->setBearbeitungsstand( $this->request->getArgument('bearbeitungsstand') );

		$bearbeitungsstatus_uuid = $this->request->getArgument('bearbeitungsstatus');
		$bearbeitungsstatus = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatus_uuid);
		$kloster->setBearbeitungsstatus($bearbeitungsstatus);

		$bearbeiter_uuid = $this->request->getArgument('bearbeiter');
		$bearbeiter = $this->bearbeiterRepository->findByIdentifier($bearbeiter_uuid);
		$kloster->setBearbeiter($bearbeiter);

		$personallistenstatus_uuid = $this->request->getArgument('personallistenstatus');
		$personallistenstatus = $this->personallistenstatusRepository->findByIdentifier($personallistenstatus_uuid);
		$kloster->setPersonallistenstatus($personallistenstatus);

		$band_uuid = $this->request->getArgument('band');

		if (isset($band_uuid) && !empty($band_uuid)) {
			$band = $this->bandRepository->findByIdentifier($band_uuid);
			$kloster->setBand($band);
		}
		$this->klosterRepository->add($kloster);
		$uuid = $kloster->getUUID();

		// Add Klosterstandort
		$ortArr = $this->request->getArgument('ort');
		$bistumArr = $this->request->getArgument('bistum');
		$gruenderArr = $this->request->getArgument('gruender');
		$breiteArr = $this->request->getArgument('breite');
		$laengeArr = $this->request->getArgument('laenge');
		$bemerkungArr = $this->request->getArgument('standortbemerkung');
		$bemerkung_standortArr = $this->request->getArgument('bemerkung_standort');
		$temp_literatur_altArr = $this->request->getArgument('temp_literatur_alt');
		$von_vonArr = $this->request->getArgument('von_von');
		$von_bisArr = $this->request->getArgument('von_bis');
		$von_verbalArr = $this->request->getArgument('von_verbal');
		$bis_vonArr = $this->request->getArgument('bis_von');
		$bis_bisArr = $this->request->getArgument('bis_bis');
		$bis_verbalArr = $this->request->getArgument('bis_verbal');
		if ($this->request->hasArgument('wuestung')) {
			$wuestungArr = $this->request->getArgument('wuestung');
		}
		$klosterstandortNumber = count($ortArr);
		$klosterstandortArr = array();

		for ($i = 0; $i < $klosterstandortNumber; $i++) {
			$klosterstandortArr[$i]['kloster'] = $uuid;
			$klosterstandortArr[$i]['ort'] = $ortArr[$i];
			$klosterstandortArr[$i]['bistum'] = $bistumArr[$i];
			$klosterstandortArr[$i]['gruender'] = $gruenderArr[$i];
			$klosterstandortArr[$i]['breite'] = $breiteArr[$i];
			$klosterstandortArr[$i]['laenge'] = $laengeArr[$i];
			$klosterstandortArr[$i]['bemerkung'] = $bemerkungArr[$i];
			$klosterstandortArr[$i]['bemerkung_standort'] = $bemerkung_standortArr[$i];
			$klosterstandortArr[$i]['temp_literatur_alt'] = $temp_literatur_altArr[$i];
			$klosterstandortArr[$i]['von_von'] = $von_vonArr[$i];
			$klosterstandortArr[$i]['von_bis'] = $von_bisArr[$i];
			$klosterstandortArr[$i]['von_verbal'] = $von_verbalArr[$i];
			$klosterstandortArr[$i]['bis_von'] = $bis_vonArr[$i];
			$klosterstandortArr[$i]['bis_bis'] = $bis_bisArr[$i];
			$klosterstandortArr[$i]['bis_verbal'] = $bis_verbalArr[$i];
			if (isset($wuestungArr[$i]) && !empty($wuestungArr[$i])) {
				$klosterstandortArr[$i]['wuestung'] = 1;
			} else {
				$klosterstandortArr[$i]['wuestung'] = 0;
			}
		}

		foreach ($klosterstandortArr as $ko) {
			$klosterstandort = new Klosterstandort();
			$kloster_uuid = $ko['kloster'];
			$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
			$klosterstandort->setKloster($kloster);
			$ort_uuid = $ko['ort'];
			$ort = $this->ortRepository->findByIdentifier($ort_uuid);
			$klosterstandort->setOrt($ort);
			$klosterstandort->setGruender($ko['gruender']);
			$klosterstandort->setBreite($ko['breite']);
			$klosterstandort->setLaenge($ko['laenge']);
			$klosterstandort->setBemerkung($ko['bemerkung']);
			$klosterstandort->setBemerkung_standort($ko['bemerkung_standort']);
			$klosterstandort->setTemp_literatur_alt($ko['temp_literatur_alt']);
			$klosterstandort->setVon_von($ko['von_von']);
			$klosterstandort->setVon_bis($ko['von_bis']);
			$klosterstandort->setVon_verbal($ko['von_verbal']);
			$klosterstandort->setBis_von($ko['bis_von']);
			$klosterstandort->setBis_bis($ko['bis_bis']);
			$klosterstandort->setBis_verbal($ko['bis_verbal']);
			$this->klosterstandortRepository->add($klosterstandort);
			$ort->setWuestung($ko['wuestung']);

			$bistumObject = $this->bistumRepository->findByIdentifier($ko['bistum']);
			if (is_object($bistumObject)) {
				$ort->setBistum($bistumObject);
			}

			$this->ortRepository->update($ort);
		}

		// Add Orden
		$ordenArr = $this->request->getArgument('orden');
		$orden_von_vonArr = $this->request->getArgument('orden_von_von');
		$orden_von_bisArr = $this->request->getArgument('orden_von_bis');
		$orden_von_verbalArr = $this->request->getArgument('orden_von_verbal');
		$orden_bis_vonArr = $this->request->getArgument('orden_bis_von');
		$orden_bis_bisArr = $this->request->getArgument('orden_bis_bis');
		$orden_bis_verbalArr = $this->request->getArgument('orden_bis_verbal');
		$klosterstatusArr = $this->request->getArgument('klosterstatus');
		$bemerkung_ordenArr = $this->request->getArgument('bemerkung_orden');
		$klosterordenNumber = count($ordenArr);
		$klosterordenArr = array();
		for ($i = 0; $i < $klosterordenNumber; $i++) {
			$klosterordenArr[$i]['kloster'] = $uuid;
			$klosterordenArr[$i]['orden'] = $ordenArr[$i];
			$klosterordenArr[$i]['klosterstatus'] = $klosterstatusArr[$i];
			$klosterordenArr[$i]['bemerkung_orden'] = $bemerkung_ordenArr[$i];
			$klosterordenArr[$i]['orden_von_von'] = $orden_von_vonArr[$i];
			$klosterordenArr[$i]['orden_von_bis'] = $orden_von_bisArr[$i];
			$klosterordenArr[$i]['orden_von_verbal'] = $orden_von_verbalArr[$i];
			$klosterordenArr[$i]['orden_bis_von'] = $orden_bis_vonArr[$i];
			$klosterordenArr[$i]['orden_bis_bis'] = $orden_bis_bisArr[$i];
			$klosterordenArr[$i]['orden_bis_verbal'] = $orden_bis_verbalArr[$i];
		}

		foreach ($klosterordenArr as $ko) {
			$klosterorden = new Klosterorden();
			$kloster_uuid = $ko['kloster'];
			$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
			$klosterorden->setKloster($kloster);
			$klosterorden->setVon_von($ko['orden_von_von']);
			$klosterorden->setVon_bis($ko['orden_von_bis']);
			$klosterorden->setVon_verbal($ko['orden_von_verbal']);
			$klosterorden->setBis_von($ko['orden_bis_von']);
			$klosterorden->setBis_bis($ko['orden_bis_bis']);
			$klosterorden->setBis_verbal($ko['orden_bis_verbal']);
			$orden_uuid = $ko['orden'];
			$orden = $this->ordenRepository->findByIdentifier($orden_uuid);
			$klosterorden->setOrden($orden);
			$klosterstatus_uuid = $ko['klosterstatus'];
			$klosterstatus = $this->klosterstatusRepository->findByIdentifier($klosterstatus_uuid);
			$klosterorden->setKlosterstatus($klosterstatus);
			$klosterorden->setBemerkung($ko['bemerkung_orden']);
			$this->klosterordenRepository->add($klosterorden);
		}

		if ($this->request->hasArgument('literatur')) {
			$kloster_uuid = $uuid;
			$literaturArr = $this->request->getArgument('literatur');
			foreach ($literaturArr as $lit) {
				$klosterHasLiteratur = new KlosterHasLiteratur();
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$literatur = $this->literaturRepository->findByIdentifier($lit);
				$klosterHasLiteratur->setKloster($kloster);
				$klosterHasLiteratur->setLiteratur($literatur);
				$this->klosterHasLiteraturRepository->add($klosterHasLiteratur);
			}
		}

		// Add GND if set
		if ($this->request->hasArgument('gnd')) {
			$gnd = $this->request->getArgument('gnd');
			if (isset($gnd) && !empty($gnd)) {
				$url = new Url();
				$url->setUrl($gnd);
				$urlTypObj = $this->urltypRepository->findOneByName('GND');
				$url->setUrltyp($urlTypObj);
				$this->urlRepository->add($url);
				$urlUUID = $url->getUUID();
				$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
				$klosterhasurl = new KlosterHasUrl();
				$klosterhasurl->setKloster($kloster);
				$klosterhasurl->setUrl($urlObj);
				$this->klosterHasUrlRepository->add($klosterhasurl);
			}
		}

		// Add Wikipedia if set
		if ($this->request->hasArgument('wikipedia')) {
			$wikipedia = $this->request->getArgument('wikipedia');
			if (isset($wikipedia) && !empty($wikipedia)) {
				$url = new Url();
				$url->setUrl($wikipedia);
				$urlTypObj = $this->urltypRepository->findOneByName('Wikipedia');
				$url->setUrltyp($urlTypObj);
				$this->urlRepository->add($url);
				$urlUUID = $url->getUUID();
				$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
				$klosterhasurl = new KlosterHasUrl();
				$klosterhasurl->setKloster($kloster);
				$klosterhasurl->setUrl($urlObj);
				$this->klosterHasUrlRepository->add($klosterhasurl);
			}
		}

		$status = 201;
		return json_encode(array($uuid));
	}

	/**
	 * @FLOW\SkipCsrfProtection
	 * @return $kloster_id The id of the kloster in json format
	 */
	public function addKlosterIdAction() {
		$uuid = $this->request->getArgument('uuid');
		$klosterObject = $this->klosterRepository->findByIdentifier($uuid);
		$kloster_uid = $klosterObject->getUid();
		$klosterObject->setKloster_id($kloster_uid);
		$this->klosterRepository->update($klosterObject);
		$this->persistenceManager->persistAll();
		return json_encode(array($uuid));
//		return json_encode(array($kloster_uid));
	}

	/**
	 * Return the data of the selected Kloster entry to be updated
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return array $response The data of the selected Kloster entry in json format
	 */
	public function editAction(Kloster $kloster) {
		// Kloster data
		$klosterArr = array();
		$klosterArr['uuid'] = $kloster->getUUID();
		$klosterArr['uid'] = $kloster->getUid();
		$klosterArr['kloster_name'] = $kloster->getKloster();
		$klosterArr['kloster_id'] = $kloster->getKloster_id();
		$klosterArr['patrozinium'] = $kloster->getPatrozinium();
		$klosterArr['bemerkung'] = $kloster->getBemerkung();
		$klosterArr['band_seite'] = $kloster->getBand_seite();
		$klosterArr['text_gs_band'] = $kloster->getText_gs_band();
		$klosterArr['bearbeitungsstand'] = $kloster->getBearbeitungsstand();
		$creationdate = $kloster->getCreationDate()->format('d.m.Y');
		$klosterArr['creationdate'] = $creationdate;
		$band = $kloster->getBand();
		if (is_object($band)) {
			$klosterArr['band'] = $band->getUUID();
		}
		$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
		$klosterArr['bearbeitungsstatus'] = $bearbeitungsstatus->getUUID();
		$personallistenstatus = $kloster->getPersonallistenstatus();
		$klosterArr['personallistenstatus'] = $personallistenstatus->getUUID();
		$bearbeiter = $kloster->getBearbeiter();
		$klosterArr['bearbeiter'] = $bearbeiter->getBearbeiter();
		$klosterArr['changeddate'] = $kloster->getChangedDate();

		// Klosterstandort data
		$klosterstandorte = array();
		$klosterstandorts = $kloster->getKlosterstandorts();
		foreach ($klosterstandorts as $i => $klosterstandort) {
			$klosterstandorte[$i]['breite'] = $klosterstandort->getBreite();
			$klosterstandorte[$i]['laenge'] = $klosterstandort->getLaenge();
			$klosterstandorte[$i]['gruender'] = $klosterstandort->getGruender();
			$klosterstandorte[$i]['bemerkung_standort'] = $klosterstandort->getBemerkung_standort();
			$klosterstandorte[$i]['standort_interne_bemerkung'] = $klosterstandort->getBemerkung();

			$ort = $klosterstandort->getOrt();
			$klosterstandorte[$i]['uuid'] = $ort->getUUID();
			$klosterstandorte[$i]['ort'] = $ort->getFullOrt();
			$klosterstandorte[$i]['wuestung'] = $ort->getWuestung();

			$bistumObject = $ort->getBistum();
			if (is_object($bistumObject)) {
				$klosterstandorte[$i]['bistum'] = $bistumObject->getUUID();
			}

			if ($klosterstandort->getVon_von()) {
				$klosterstandorte[$i]['von_von'] = $klosterstandort->getVon_von();
			}
			if ($klosterstandort->getVon_bis()) {
				$klosterstandorte[$i]['von_bis'] = $klosterstandort->getVon_bis();
			}
			$klosterstandorte[$i]['von_verbal'] = $klosterstandort->getVon_verbal();
			if ($klosterstandort->getBis_von()) {
				$klosterstandorte[$i]['bis_von'] = $klosterstandort->getBis_von();
			}
			if ($klosterstandort->getBis_bis()) {
				$klosterstandorte[$i]['bis_bis'] = $klosterstandort->getBis_bis();
			}
			$klosterstandorte[$i]['bis_verbal'] = $klosterstandort->getBis_verbal();
		}
		$klosterArr['klosterstandorte'] = $klosterstandorte;

		// Klosterorden data
		$klosterorden = array();
		$klosterordens = $kloster->getKlosterordens();
		foreach ($klosterordens as $j => $ko) {
			$klosterorden[$j]['bemerkung_orden'] = $ko->getBemerkung();
			$orden = $ko->getOrden();
			$klosterorden[$j]['orden'] = $orden->getUUID();
			$klosterstatus = $ko->getKlosterstatus();
			$klosterorden[$j]['klosterstatus'] = $klosterstatus->getUUID();
			if ($ko->getVon_von()) {
				$klosterorden[$j]['orden_von_von'] = $ko->getVon_von();
			}
			if ($ko->getVon_bis()) {
				$klosterorden[$j]['orden_von_bis'] = $ko->getVon_bis();
			}
			$klosterorden[$j]['orden_von_verbal'] = $ko->getVon_verbal();
			if ($ko->getBis_von()) {
				$klosterorden[$j]['orden_bis_von'] = $ko->getBis_von();
			}
			if ($ko->getBis_bis()) {
				$klosterorden[$j]['orden_bis_bis'] = $ko->getBis_bis();
			}
			$klosterorden[$j]['orden_bis_verbal'] = $ko->getBis_verbal();
		}
		$klosterArr['klosterorden'] = $klosterorden;

		// Kloster Url data
		$Urls = array();
		$klosterHasUrls = $kloster->getKlosterHasUrls();
		foreach ($klosterHasUrls as $k => $klosterHasUrl) {
			$urlObj = $klosterHasUrl->getUrl();
			$url = rawurldecode($urlObj->getUrl());
			$urlTypObj = $urlObj->getUrltyp();
			$urlTyp = $urlTypObj->getName();
			$Urls[$k] = array('url_typ' => $urlTyp, 'url' => $url);
		}
		$klosterArr['url'] = $Urls;

		// Kloster Literature data
		$Literaturs = array();
		$klosterHasLiteraturs = $kloster->getKlosterHasLiteraturs();
		foreach ($klosterHasLiteraturs as $l => $klosterHasLiteratur) {
			$literaturObj = $klosterHasLiteratur->getLiteratur();
			$literatur = $literaturObj->getUUID();
			$Literaturs[$l] = $literatur;
		}
		$klosterArr['literatur'] = $Literaturs;

		return json_encode($klosterArr);

	}

	/**
	 * Return the options to fill the select fields in Kloster edit form
	 * @FLOW\SkipCsrfProtection
	 * @return array $response select options as JSON
	 */
	public function getOptionsAction() {

		// Bearbeitungsstatus data
		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n => $bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		// Personallistenstatus data
		$personallistenstatusArr = array();
		$personallistenstatuses = $this->personallistenstatusRepository->findAll();
		foreach ($personallistenstatuses as $m => $personallistenstatus) {
			$personallistenstatusArr[$m] = array($personallistenstatus->getName() => $personallistenstatus->getUUID());
		}

		// Band data
		$bandArr = array();
		$this->bandRepository->setDefaultOrderings(
				array('titel' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bands = $this->bandRepository->findAll();
		foreach ($bands as $p => $band) {
			if ($band->getTitel() != 'keine Angabe') {
				$bandNummerTitel = $band->getNummer() . '-' . $band->getTitel();
			}
			else {
				 $bandNummerTitel = $band->getTitel();
			}
			$bandArr[$p] = array($bandNummerTitel => $band->getUUID());
		}

		// Literature data for select box
		$literaturArr = array();
		$this->literaturRepository->setDefaultOrderings(
				array('citekey' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$literaturs = $this->literaturRepository->findAll();
		foreach ($literaturs as $q => $literatur) {
			$literatur_name = $literatur->getCitekey();
			$literatur_beschreibung = $literatur->getBeschreibung();
			if (null !== $literatur_beschreibung && !empty($literatur_beschreibung)) $literatur_name .= "(" . $literatur_beschreibung . ")";
			$literaturArr[$q] = array($literatur_name => $literatur->getUUID());
		}

		// Bistum data for select box
		$bistumArr = array();
		$this->bistumRepository->setDefaultOrderings(
				array('bistum' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bistums = $this->bistumRepository->findAll();
		foreach ($bistums as $r => $bistum) {
			$bistumArr[$r] = array($bistum->getBistum() => $bistum->getUUID());
		}

		// Orden data for select box
		$ordenArr = array();
		$this->ordenRepository->setDefaultOrderings(
				array('orden' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$ordens = $this->ordenRepository->findAll();
		foreach ($ordens as $m => $orden) {
			$ordenArr[$m] = array($orden->getOrden() => $orden->getUUID());
		}

		// Klosterstatus data for select box
		$klosterstatusArr = array();
		$this->klosterstatusRepository->setDefaultOrderings(
				array('status' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$klosterstatuses = $this->klosterstatusRepository->findAll();
		foreach ($klosterstatuses as $n => $klosterstatus) {
			$klosterstatusArr[$n] = array($klosterstatus->getStatus() => $klosterstatus->getUUID());
		}

		// Bearbeiter data for select box
		$bearbeiterArr = array();
		$this->bearbeiterRepository->setDefaultOrderings(
				array('bearbeiter' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bearbeiters = $this->bearbeiterRepository->findAll();
		foreach ($bearbeiters as $q => $bearbeiter) {
			$bearbeiterArr[$q] = array($bearbeiter->getBearbeiter() => $bearbeiter->getUUID());
		}

		$response = array();
		$response[] = $bearbeitungsstatusArr;
		$response[] = $personallistenstatusArr;
		$response[] = $bandArr;
		$response[] = $literaturArr;
		$response[] = $bistumArr;
		$response[] = $ordenArr;
		$response[] = $klosterstatusArr;
		$response[] = $bearbeiterArr;
		return json_encode($response);

	}

	/** Update data of a selected Kloster
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return integer The http status
	 **/
	public function updateAction(Kloster $kloster) {

		// Update Kloster
		$param = $this->request->getArguments();
		$id = $param['kloster']['__identity'];
		$kloster = $this->klosterRepository->findByIdentifier($id);
		$kloster->setKloster( $this->request->getArgument('kloster_name') );
		$kloster->setPatrozinium( $this->request->getArgument('patrozinium') );
		$kloster->setBemerkung( $this->request->getArgument('bemerkung') );
		$kloster->setBand_seite( $this->request->getArgument('band_seite') );
		$kloster->setText_gs_band( $this->request->getArgument('text_gs_band') );
		$kloster->setBearbeitungsstand( $this->request->getArgument('bearbeitungsstand') );
		$bearbeitungsstatus_uuid = $this->request->getArgument('bearbeitungsstatus');
		$bearbeitungsstatus = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatus_uuid);
		$kloster->setBearbeitungsstatus($bearbeitungsstatus);
		//$bearbeiter_uuid = $this->request->getArgument('bearbeiter');
		//$bearbeiter = $this->bearbeiterRepository->findByIdentifier($bearbeiter_uuid);
		//$kloster->setBearbeiter($bearbeiter);
		//$personallistenstatus_uuid = $this->request->getArgument('personallistenstatus');
		//$personallistenstatus = $this->personallistenstatusRepository->findByIdentifier($personallistenstatus_uuid);
		//$kloster->setPersonallistenstatus($personallistenstatus);
		$band_uuid = $this->request->getArgument('band');
		$band = $this->bandRepository->findByIdentifier($band_uuid);
		$kloster->setBand($band);
		$this->klosterRepository->update($kloster);

		// Update Klosterstandort
		$ortArr = $this->request->getArgument('ort');
		$bistumArr = $this->request->getArgument('bistum');
		$gruenderArr = $this->request->getArgument('gruender');
		$breiteArr = $this->request->getArgument('breite');
		$laengeArr = $this->request->getArgument('laenge');
		$bemerkung_standortArr = $this->request->getArgument('bemerkung_standort');
		$bemerkungArr = $this->request->getArgument('standort_interne_bemerkung');
		$von_vonArr = $this->request->getArgument('von_von');
		$von_bisArr = $this->request->getArgument('von_bis');
		$von_verbalArr = $this->request->getArgument('von_verbal');
		$bis_vonArr = $this->request->getArgument('bis_von');
		$bis_bisArr = $this->request->getArgument('bis_bis');
		$bis_verbalArr = $this->request->getArgument('bis_verbal');
		if ($this->request->hasArgument('wuestung')) {
			$wuestungArr = $this->request->getArgument('wuestung');
		}
		$klosterstandortNumber = count($ortArr);
		$klosterstandortArr = array();
		for ($i = 0; $i < $klosterstandortNumber; $i++) {
			$klosterstandortArr[$i]['kloster'] = $id;
			$klosterstandortArr[$i]['ort'] = $ortArr[$i];
			$klosterstandortArr[$i]['bistum'] = $bistumArr[$i];
			$klosterstandortArr[$i]['gruender'] = $gruenderArr[$i];
			$klosterstandortArr[$i]['breite'] = $breiteArr[$i];
			$klosterstandortArr[$i]['laenge'] = $laengeArr[$i];
			$klosterstandortArr[$i]['bemerkung_standort'] = $bemerkung_standortArr[$i];
			$klosterstandortArr[$i]['bemerkung'] = $bemerkungArr[$i];
			$klosterstandortArr[$i]['von_von'] = $von_vonArr[$i];
			$klosterstandortArr[$i]['von_bis'] = $von_bisArr[$i];
			$klosterstandortArr[$i]['von_verbal'] = $von_verbalArr[$i];
			$klosterstandortArr[$i]['bis_von'] = $bis_vonArr[$i];
			$klosterstandortArr[$i]['bis_bis'] = $bis_bisArr[$i];
			$klosterstandortArr[$i]['bis_verbal'] = $bis_verbalArr[$i];
			if (isset($wuestungArr[$i]) && !empty($wuestungArr[$i])) {
				$klosterstandortArr[$i]['wuestung'] = 1;
			} else {
				$klosterstandortArr[$i]['wuestung'] = 0;
			}
		}

		if (isset($klosterstandortArr) && !empty($klosterstandortArr) && is_array($klosterstandortArr)) {
			$klosterstandorts = $kloster->getKlosterstandorts();
			foreach ($klosterstandorts as $i => $klosterstandort) {
				$this->klosterstandortRepository->remove($klosterstandort);
			}
			foreach ($klosterstandortArr as $ko) {
				$klosterstandort = new Klosterstandort();
				$kloster_uuid = $ko['kloster'];
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$klosterstandort->setKloster($kloster);
				$ort_uuid = $ko['ort'];
				$ort = $this->ortRepository->findByIdentifier($ort_uuid);
				$klosterstandort->setOrt($ort);
				$klosterstandort->setGruender($ko['gruender']);
				$klosterstandort->setBreite($ko['breite']);
				$klosterstandort->setLaenge($ko['laenge']);
				$klosterstandort->setVon_von($ko['von_von']);
				$klosterstandort->setVon_bis($ko['von_bis']);
				$klosterstandort->setVon_verbal($ko['von_verbal']);
				$klosterstandort->setBis_von($ko['bis_von']);
				$klosterstandort->setBis_bis($ko['bis_bis']);
				$klosterstandort->setBis_verbal($ko['bis_verbal']);
				$klosterstandort->setBemerkung_standort($ko['bemerkung_standort']);
				$klosterstandort->setBemerkung($ko['bemerkung']);
				$this->klosterstandortRepository->add($klosterstandort);
				$ort->setWuestung($ko['wuestung']);
				$bistumObject = $this->bistumRepository->findByIdentifier($ko['bistum']);
				if (is_object($bistumObject)) {
					$ort->setBistum($bistumObject);
				}
				$this->ortRepository->update($ort);
			}
		}

		// Update Orden
		$ordenArr = $this->request->getArgument('orden');
		$klosterstatusArr = $this->request->getArgument('klosterstatus');
		$bemerkung_ordenArr = $this->request->getArgument('bemerkung_orden');
		$orden_von_vonArr = $this->request->getArgument('orden_von_von');
		$orden_von_bisArr = $this->request->getArgument('orden_von_bis');
		$orden_von_verbalArr = $this->request->getArgument('orden_von_verbal');
		$orden_bis_vonArr = $this->request->getArgument('orden_bis_von');
		$orden_bis_bisArr = $this->request->getArgument('orden_bis_bis');
		$orden_bis_verbalArr = $this->request->getArgument('orden_bis_verbal');
		$klosterordenNumber = count($ordenArr);
		$klosterordenArr = array();
		for ($i = 0; $i < $klosterordenNumber; $i++) {
			$klosterordenArr[$i]['kloster'] = $id;
			$klosterordenArr[$i]['orden'] = $ordenArr[$i];
			$klosterordenArr[$i]['klosterstatus'] = $klosterstatusArr[$i];
			$klosterordenArr[$i]['bemerkung_orden'] = $bemerkung_ordenArr[$i];
			$klosterordenArr[$i]['orden_von_von'] = $orden_von_vonArr[$i];
			$klosterordenArr[$i]['orden_von_bis'] = $orden_von_bisArr[$i];
			$klosterordenArr[$i]['orden_von_verbal'] = $orden_von_verbalArr[$i];
			$klosterordenArr[$i]['orden_bis_von'] = $orden_bis_vonArr[$i];
			$klosterordenArr[$i]['orden_bis_bis'] = $orden_bis_bisArr[$i];
			$klosterordenArr[$i]['orden_bis_verbal'] = $orden_bis_verbalArr[$i];
		}
		if (isset($klosterordenArr) && !empty($klosterordenArr) && is_array($klosterordenArr)) {
			$klosterordens = $kloster->getKlosterordens();
			foreach ($klosterordens as $i => $klosterorden) {
				$this->klosterordenRepository->remove($klosterorden);
			}
			foreach ($klosterordenArr as $ko) {
				$klosterorden = new Klosterorden();
				$kloster_uuid = $ko['kloster'];
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$klosterorden->setKloster($kloster);
				$klosterorden->setVon_von($ko['orden_von_von']);
				$klosterorden->setVon_bis($ko['orden_von_bis']);
				$klosterorden->setVon_verbal($ko['orden_von_verbal']);
				$klosterorden->setBis_von($ko['orden_bis_von']);
				$klosterorden->setBis_bis($ko['orden_bis_bis']);
				$klosterorden->setBis_verbal($ko['orden_bis_verbal']);
				$orden_uuid = $ko['orden'];
				$orden = $this->ordenRepository->findByIdentifier($orden_uuid);
				$klosterorden->setOrden($orden);
				$klosterstatus_uuid = $ko['klosterstatus'];
				$klosterstatus = $this->klosterstatusRepository->findByIdentifier($klosterstatus_uuid);
				$klosterorden->setKlosterstatus($klosterstatus);
				$klosterorden->setBemerkung($ko['bemerkung_orden']);
				$this->klosterordenRepository->add($klosterorden);
			}
		}

		// Update literatur
		$literaturs = $kloster->getKlosterHasLiteraturs();
		foreach ($literaturs as $literatur) {
			$this->klosterHasLiteraturRepository->remove($literatur);
		}

		if ($this->request->hasArgument('literatur')) {
			$literaturArr = $this->request->getArgument('literatur');
			if (isset($literaturArr) && !empty($literaturArr) && is_array($literaturArr)) {
				foreach ($literaturArr as $lit) {
					if (isset($lit) && !empty($lit)) {
						$klosterHasLiteratur = new KlosterHasLiteratur();
						$kloster = $this->klosterRepository->findByIdentifier($id);
						$literatur = $this->literaturRepository->findByIdentifier($lit);
						$klosterHasLiteratur->setKloster($kloster);
						$klosterHasLiteratur->setLiteratur($literatur);
						$this->klosterHasLiteraturRepository->add($klosterHasLiteratur);
					}
				}
			}
		}

		// Fetch Kloster Urls
		$klosterHasUrls = $kloster->getKlosterHasUrls();
		$klosterHasGND = false;

		// Update GND if set
		if ($this->request->hasArgument('gnd')) {
			$gnd = $this->request->getArgument('gnd');
			if (isset($gnd) && !empty($gnd)) {
				foreach ($klosterHasUrls as $i => $klosterHasUrl) {
					$urlObj = $klosterHasUrl->getUrl();
					$url = $urlObj->getUrl();
					$urlTypObj = $urlObj->getUrltyp();
					$urlTyp = $urlTypObj->getName();
					if ($urlTyp == "GND") {
						$urlObj->setUrl($gnd);
						$this->urlRepository->update($urlObj);
						$klosterHasGND = true;
					}
				}
				if (!$klosterHasGND) {
					$url = new Url();
					$url->setUrl($gnd);
					$urlTypObj = $this->urltypRepository->findOneByName('GND');
					$url->setUrltyp($urlTypObj);
					$this->urlRepository->add($url);
					$urlUUID = $url->getUUID();
					$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
					$klosterhasurl = new KlosterHasUrl();
					$klosterhasurl->setKloster($kloster);
					$klosterhasurl->setUrl($urlObj);
					$this->klosterHasUrlRepository->add($klosterhasurl);
				}
			}
		}

		//Update Wikipedia if set
		$klosterHasWiki = false;
		if ($this->request->hasArgument('wikipedia')) {
			$wikipedia = $this->request->getArgument('wikipedia');
			if (isset($wikipedia) && !empty($wikipedia)) {
				foreach ($klosterHasUrls as $i => $klosterHasUrl) {
					$urlObj = $klosterHasUrl->getUrl();
					$url = $urlObj->getUrl();
					$urlTypObj = $urlObj->getUrltyp();
					$urlTyp = $urlTypObj->getName();
					if ($urlTyp == "Wikipedia") {
						$urlObj->setUrl($wikipedia);
						$this->urlRepository->update($urlObj);
						$klosterHasWiki = true;
					}
				}
				if (!$klosterHasWiki) {
					$url = new Url();
					$url->setUrl($wikipedia);
					$urlTypObj = $this->urltypRepository->findOneByName('Wikipedia');
					$url->setUrltyp($urlTypObj);
					$this->urlRepository->add($url);
					$urlUUID = $url->getUUID();
					$urlObj = $this->urlRepository->findByIdentifier($urlUUID);
					$klosterhasurl = new KlosterHasUrl();
					$klosterhasurl->setKloster($kloster);
					$klosterhasurl->setUrl($urlObj);
					$this->klosterHasUrlRepository->add($klosterhasurl);
				}
			}
		}

		$status = 200;
		return json_encode(array($status));

	}

	/**
	 * Delete a selected Kloster entry
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return integer $status The http status
	 */
	public function deleteAction(Kloster $kloster) {
		$this->klosterRepository->remove($kloster);
		$klosterordens = $kloster->getKlosterordens();
		if (is_array($klosterordens)) {
			foreach ($klosterordens as $i => $klosterorden) {
				$this->klosterordenRepository->remove($klosterorden);
			}
		}
		$klosterstandorts = $kloster->getKlosterstandorts();
		if (is_array($klosterstandorts)) {
			foreach ($klosterstandorts as $i => $klosterstandort) {
				$this->klosterstandortRepository->remove($klosterstandort);
			}
		}
		$literaturs = $kloster->getKlosterHasLiteraturs();
		if (is_array($literaturs)) {
			foreach ($literaturs as $literatur) {
				$this->klosterHasLiteraturRepository->remove($literatur);
			}
		}
		$urls = $kloster->getKlosterHasUrls();
		if (is_array($urls)) {
			foreach ($urls as $url) {
				$this->klosterHasUrlRepository->remove($url);
			}
		}
		$status = 200;
		return json_encode(array($status));
	}

	/** Gets and returns the list of Orte as per search string
	 * @param void
	 * @return array $reponse
	 */
	public function searchOrtAction() {
		if ($this->request->hasArgument('searchString')) {
			$searchString = $this->request->getArgument('searchString');
			$searchString = "%" . $searchString . "%";
			$searchResult = $this->ortRepository->findOrtBySearchString($searchString);
			$orte = array();
			foreach ($searchResult as $res) {
				$orte[] = array(
					'uuid' => $res->getUUID(),
					'name' => $res->getFullOrt()
				);
			}
			return json_encode($orte);
		}
	}

	public function getLastKlosterIdAction() {
		$result = $this->klosterRepository->findLastEntry();

		foreach ($result as $res) {
			$last_kloster_id = $res->kloster_id;
		}

		$new_kloster_id = $last_kloster_id + 1;

		return $new_kloster_id;
	}

	/** Gets and returns the list of Klosters as per search string
	 * @param void
	 * @return array $reponse
	 * @FLOW\SkipCsrfProtection
	 */
	public function searchAction() {

		if ($this->request->hasArgument('alle')) {
			$alle = $this->request->getArgument('alle');
		}

		if (isset($alle) && !empty($alle)) {
			$searchResult = $this->klosterRepository->findKlosterByWildCard($alle);
			$resultArr = array();
			foreach ($searchResult as $v) {
				$resultArr[] = $v['Persistence_Object_Identifier'];
			}

			return json_encode($resultArr);
		}

	}

}

?>