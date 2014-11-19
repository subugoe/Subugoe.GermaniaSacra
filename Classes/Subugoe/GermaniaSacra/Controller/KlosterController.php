<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Kloster;
use Subugoe\GermaniaSacra\Domain\Model\Klosterstandort;
use Subugoe\GermaniaSacra\Domain\Model\Klosterorden;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur;
use Subugoe\GermaniaSacra\Domain\Model\Url;
use Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl;
use Subugoe\GermaniaSacra\Queue\FileGenerationJob;

class KlosterController extends AbstractBaseController {

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\LandRepository
	 */
	protected $landRepository;

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenstypRepository
	 */
	protected $ordenstypRepository;

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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Policy\RoleRepository
	 */
	protected $roleRepository;

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
	 * @var \Subugoe\GermaniaSacra\Controller\ProxyController
	 * @FLOW\Inject
	 */
	protected $proxy;

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
	 * @var array
	*/
	protected  $joinParamArr = array(
									'band.nummer' => array('join' => array(array('kloster','band','band')), 'parameter' => 'band', 'duplicateJoinCheck' => array('band')),
									'bearbeitungsstatus.name' => array('join' => array(array('kloster', 'bearbeitungsstatus', 'bearbeitungsstatus')), 'parameter' => 'name', 'duplicateJoinCheck' => array('bearbeitungsstatus')),
									'personallistenstatus.name' => array('join' => array(array('kloster', 'personallistenstatus', 'personallistenstatus')), 'parameter' => 'pname', 'duplicateJoinCheck' => array('personallitenstatus')),
									'kloster.kloster' => array('' => array(array('','','')), 'parameter' => 'kloster', 'duplicateJoinCheck' => ''),
									'kloster.kloster_id' => array('' => array(array('','','')), 'parameter' => 'kloster_id', 'duplicateJoinCheck' => ''),
									'kloster.patrozinium' => array('' => array(array('','','')), 'parameter' => 'patrozinium', 'duplicateJoinCheck' => ''),
									'klosterstandort.breite' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'parameter' => 'breite', 'duplicateJoinCheck' => array('klosterstandorts')),
									'klosterstandort.laenge' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'parameter' => 'laenge', 'duplicateJoinCheck' => array('klosterstandorts')),
									'klosterstandort.von_von' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'parameter' => 'von_von', 'duplicateJoinCheck' => array('klosterstandorts')),
									'klosterstandort.bis_bis' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'parameter' => 'bis_bis', 'duplicateJoinCheck' => array('klosterstandorts')),
									'ort.ort' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'secondjoin' => array(array('klosterstandort', 'ort', 'ort')), 'parameter' => 'ort', 'duplicateJoinCheck' => array('klosterstandorts', 'ort')),
									'bistum.bistum' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'secondjoin' => array(array('klosterstandort', 'ort', 'ort')), 'thirdjoin' => array(array('ort', 'bistum', 'bistum')), 'parameter' => 'bistum', 'duplicateJoinCheck' => array('klosterstandorts', 'ort', 'bistum')),
									'land.land' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'secondjoin' => array(array('klosterstandort', 'ort', 'ort')), 'thirdjoin' => array(array('ort', 'land', 'land')), 'parameter' => 'land', 'duplicateJoinCheck' => array('klosterstandorts', 'ort', 'land')),
									'orden.orden' => array('join' => array(array('kloster', 'klosterordens', 'klosterorden')), 'secondjoin' => array(array('klosterorden', 'orden', 'orden')), 'parameter' => 'orden', 'duplicateJoinCheck' => array('klosterorden', 'orden')),
									'klosterstatus.status' => array('join' => array(array('kloster', 'klosterordens', 'klosterorden')), 'secondjoin' => array(array('klosterorden', 'klosterstatus', 'klosterstatus')), 'parameter' => 'status', 'duplicateJoinCheck' => array('klosterorden', 'klosterstatus')),
									'url.url' => array('join' => array(array('kloster', 'klosterHasUrls', 'klosterhasurl')), 'secondjoin' => array(array('klosterhasurl', 'url', 'url')), 'parameter' => 'url', 'duplicateJoinCheck' => array('klosterhasurl', 'url')),
									'bundesland.bundesland' => array('join' => array(array('kloster', 'klosterstandorts', 'klosterstandort')), 'secondjoin' => array(array('klosterstandort', 'ort', 'ort')), 'thirdjoin' => array(array('ort', 'land', 'land')), 'parameter' => 'land', 'secondparameter' => array('entity' => 'land', 'property' => 'ist_in_deutschland', 'operator' => '=', 'value_alias' => 'bundesland', 'value' => '1'), 'duplicateJoinCheck' => array('klosterstandorts', 'ort', 'land')),
									);

	/**
	 * Fetches the actual Bearbeiter and assigns it to the view
	 */
	public function listAction() {
		$this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
	}

	/**
	 * @return array $response The Kloster array
	 */
	public function allAsJson() {
		$this->klosterRepository->setDefaultOrderings(
				array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING)
		);
		$klosters = $this->klosterRepository->findAll();
		$klosterArr = array();
		foreach ($klosters as $k => $kloster) {
			$klosterArr[$k]['uUID'] = $kloster->getUUID();
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
			if (!empty($klosterArr[$k]['ort'][$i])) {
				$klosterArr[$k]['ort'] = implode('\n', $klosterArr[$k]['ort']);
			}
			else {
				$klosterArr[$k]['ort'] = '';
			}
			$klosterHasUrls = $kloster->getKlosterHasUrls();
			$klosterArr[$k]['gnd'] = '';
			foreach ($klosterHasUrls as $klosterHasUrl) {
				$urlObj = $klosterHasUrl->getUrl();
				$url = $urlObj->getUrl();
				$urlTypObj = $urlObj->getUrltyp();
				if (is_object($urlTypObj)) {
					$urlTyp = $urlTypObj->getName();
					if ($urlTyp == "GND") {
						$klosterArr[$k]['gnd'] = $url;
					}
				}
			}
		}
		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $bearbeitungsstatus) {
			$bearbeitungsstatusArr[] = array('uuid' => $bearbeitungsstatus->getUUID(), 'name' => $bearbeitungsstatus->getName());
		}
		$response = array();
		$response['data'] = $klosterArr;
		$response['bearbeitungsstatus'] = $bearbeitungsstatusArr;
		return json_encode($response);
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
	 * @return void
	 */
	public function createAction() {
		$lastKlosterId = $this->getLastKlosterIdAction();
		if (!empty($lastKlosterId)) {
			$kloster_uid = $lastKlosterId + 1;
			$kloster = new Kloster();
			$kloster->setUid($kloster_uid);
			$kloster->setKloster_id($kloster_uid);
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
			$kloster->setBearbeiter($this->bearbeiterObj);
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
			if ($this->request->hasArgument('ort')) {
				$ortArr = $this->request->getArgument('ort');
			}
			if ($this->request->hasArgument('bistum')) {
				$bistumArr = $this->request->getArgument('bistum');
			}
			$gruenderArr = $this->request->getArgument('gruender');
			$breiteArr = $this->request->getArgument('breite');
			$laengeArr = $this->request->getArgument('laenge');
			$bemerkungArr = $this->request->getArgument('bemerkung_standort');
			$bemerkung_standortArr = $this->request->getArgument('bemerkung_standort');
			if ($this->request->hasArgument('temp_literatur_alt')) {
				$temp_literatur_altArr = $this->request->getArgument('temp_literatur_alt');
			}
			$von_vonArr = $this->request->getArgument('von_von');
			$von_bisArr = $this->request->getArgument('von_bis');
			$von_verbalArr = $this->request->getArgument('von_verbal');
			$bis_vonArr = $this->request->getArgument('bis_von');
			$bis_bisArr = $this->request->getArgument('bis_bis');
			$bis_verbalArr = $this->request->getArgument('bis_verbal');
			if ($this->request->hasArgument('wuestung')) {
				$wuestungArr = $this->request->getArgument('wuestung');
			}
			if (isset($ortArr) && !empty($ortArr)) {
				$klosterstandortNumber = count($ortArr);
			}
			else {
				$klosterstandortNumber = count($bistumArr);
			}
			$klosterstandortArr = array();
			for ($i = 0; $i < $klosterstandortNumber; $i++) {
				$klosterstandortArr[$i]['kloster'] = $uuid;
				if (isset($ortArr[$i]) && !empty($ortArr[$i])) {
					$klosterstandortArr[$i]['ort'] = $ortArr[$i];
				}
				if (isset($bistumArr[$i]) && !empty($bistumArr[$i])) {
					$klosterstandortArr[$i]['bistum'] = $bistumArr[$i];
				}
				$klosterstandortArr[$i]['gruender'] = $gruenderArr[$i];
				$klosterstandortArr[$i]['breite'] = $breiteArr[$i];
				$klosterstandortArr[$i]['laenge'] = $laengeArr[$i];
				$klosterstandortArr[$i]['bemerkung'] = $bemerkungArr[$i];
				$klosterstandortArr[$i]['bemerkung_standort'] = $bemerkung_standortArr[$i];
				if (isset($klosterstandortArr[$i]['temp_literatur_alt']) && !empty($klosterstandortArr[$i]['temp_literatur_alt'])) {
					$klosterstandortArr[$i]['temp_literatur_alt'] = $temp_literatur_altArr[$i];
				}
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
			$lastKlosterstandortId = $this->getLastKlosterstandortIdAction();
			foreach ($klosterstandortArr as $ko) {
				$klosterstandort = new Klosterstandort();
				$kloster_uuid = $ko['kloster'];
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$klosterstandort->setUid(++$lastKlosterstandortId);
				$klosterstandort->setKloster($kloster);

				if (!empty($ko['ort'])) {
					$ort_uuid = $ko['ort'];
					$ort = $this->ortRepository->findByIdentifier($ort_uuid);
					$klosterstandort->setOrt($ort);
					$ort->setWuestung($ko['wuestung']);
					if (!empty($ko['bistum'])) {
						$bistumObject = $this->bistumRepository->findByIdentifier($ko['bistum']);
						if (is_object($bistumObject)) {
							$ort->setBistum($bistumObject);
						}
					}
					$this->ortRepository->update($ort);
				}
				$klosterstandort->setGruender($ko['gruender']);
				$klosterstandort->setBreite($ko['breite']);
				$klosterstandort->setLaenge($ko['laenge']);
				$klosterstandort->setBemerkung($ko['bemerkung']);
				$klosterstandort->setBemerkung_standort($ko['bemerkung_standort']);
				if (isset($ko['temp_literatur_alt']) && !empty($ko['temp_literatur_alt'])) {
					$klosterstandort->setTemp_literatur_alt($ko['temp_literatur_alt']);
				}
				$klosterstandort->setVon_von($ko['von_von']);
				$klosterstandort->setVon_bis($ko['von_bis']);
				$klosterstandort->setVon_verbal($ko['von_verbal']);
				$klosterstandort->setBis_von($ko['bis_von']);
				$klosterstandort->setBis_bis($ko['bis_bis']);
				$klosterstandort->setBis_verbal($ko['bis_verbal']);
				$this->klosterstandortRepository->add($klosterstandort);
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
			$lastKlosterordentId = $this->getLastKlosterordenIdAction();
			foreach ($klosterordenArr as $ko) {
				$klosterorden = new Klosterorden();
				$kloster_uuid = $ko['kloster'];
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$klosterorden->setUid(++$lastKlosterordentId);
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

				if (is_array(($laengeArr) && !empty($literaturArr))) {
					foreach ($literaturArr as $lit) {
						$klosterHasLiteratur = new KlosterHasLiteratur();
						$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
						$literatur = $this->literaturRepository->findByIdentifier($lit);
						$klosterHasLiteratur->setKloster($kloster);
						$klosterHasLiteratur->setLiteratur($literatur);
						$this->klosterHasLiteraturRepository->add($klosterHasLiteratur);
					}
				}
			}
			// Add GND if set
			if ($this->request->hasArgument('gnd')) {
				$gnd = $this->request->getArgument('gnd');
				if (isset($gnd) && !empty($gnd)) {
					$url = new Url();
					$url->setUrl($gnd);
					if ($this->request->hasArgument('gnd_label')) {
						$gnd_label = $this->request->getArgument('gnd_label');
					}
					if (empty($gnd_label)) {
						$gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
						$gnd_label = $kloster . ' [' . $gndid . ']';
					}
					if (!empty($gnd_label)) {
						$url->setBemerkung($gnd_label);
					}
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
					if ($this->request->hasArgument('wikipedia_label')) {
						$wikipedia_label = $this->request->getArgument('wikipedia_label');
					}
					if (empty($wikipedia_label)) {
						$wikipedia_label = str_replace('http://de.wikipedia.org/wiki/', '', trim($wikipedia));
						$wikipedia_label = str_replace('_', ' ', $wikipedia_label);
						$wikipedia_label = rawurldecode($wikipedia_label);
					}
					if (!empty($wikipedia_label)) {
						$url->setBemerkung($wikipedia_label);
					}
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
			// Add Url if set
			if ($this->request->hasArgument('url')) {
				$urlArr = $this->request->getArgument('url');
				if (isset($urlArr) && $urlArr !== array()) {
					if ($this->request->hasArgument('url_typ')) {
						$urlTypArr = $this->request->getArgument('url_typ');
					}
					if ($this->request->hasArgument('links_label')) {
						$linksLabelArr = $this->request->getArgument('links_label');
					}
					if ((isset($urlArr) && !empty($urlArr)) && (isset($urlTypArr) && !empty($urlTypArr))) {
						foreach ($urlArr as $k => $url) {
							if (!empty($url)) {
								$urlObj = new Url();
								$urlObj->setUrl($url);
								if (isset($linksLabelArr[$k]) && !empty($linksLabelArr[$k])) {
									$urlObj->setBemerkung($linksLabelArr[$k]);
								}
								$urlTypObj = $this->urltypRepository->findByIdentifier($urlTypArr[$k]);
								$urlObj->setUrltyp($urlTypObj);
								$this->urlRepository->add($urlObj);
								$klosterhasurlObj = new KlosterHasUrl();
								$klosterhasurlObj->setKloster($kloster);
								$klosterhasurlObj->setUrl($urlObj);
								$this->klosterHasUrlRepository->add($klosterhasurlObj);

							}
						}
					}
				}
			}
			$this->throwStatus(201, NULL, Null);
		}
		else {
			$this->throwStatus(400, 'Kloster id could not be set', Null);
		}
	}

	/**
	 * Edit a Kloster entity
	 * @return array $response The data of the selected Kloster entry in json format
	 */
	public function editAction() {
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$kloster = $this->klosterRepository->findByIdentifier($uuid);
		if (!is_object($kloster)) {
			$this->throwStatus(400, 'Entity Kloster not available', Null);
		}
		// Kloster data
		$klosterArr = array();
		$klosterArr['uUID'] = $kloster->getUUID();
		$klosterArr['uid'] = $kloster->getUid();
		$klosterArr['kloster_name'] = $kloster->getKloster();
		$klosterArr['kloster_id'] = $kloster->getKloster_id();
		$klosterArr['patrozinium'] = $kloster->getPatrozinium();
		$klosterArr['bemerkung'] = $kloster->getBemerkung();
		$klosterArr['band_seite'] = $kloster->getBand_seite();
		$klosterArr['text_gs_band'] = $kloster->getText_gs_band();
		$klosterArr['bearbeitungsstand'] = $kloster->getBearbeitungsstand();
		$creationdate = $kloster->getCreationDate()->format('d.m.Y H:i:s');
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
		if ($kloster->getChangedDate()) {
			$changeddate = $kloster->getChangedDate()->format('d.m.Y H:i:s');
			$klosterArr['changeddate'] = $changeddate;
		}
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
			$klosterstandorte[$i]['uUID'] = $ort->getUUID();
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
			$url_bemerkung = $urlObj->getBemerkung();
			if ($url !== 'keine Angabe') {
				$urlTypObj = $urlObj->getUrltyp();
				if (is_object($urlTypObj)) {
					$urlTyp = $urlTypObj->getUUID();
					$urlTypName = $urlTypObj->getName();
					if ($urlTypName == 'GND' || $urlTypName == 'Wikipedia') {
						$Urls[$k] = array('url_typ' => $urlTyp, 'url' => $url, 'url_label' => $url_bemerkung, 'url_typ_name' => $urlTypName);
					}
					else {
						$Urls[$k] = array('url_typ' => $urlTyp, 'url' => $url, 'links_label' => $url_bemerkung, 'url_typ_name' => $urlTypName);
					}
				}
			}
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
		$role = array_keys($this->securityContext->getAccount()->getRoles())[0];
		// Bearbeitungsstatus data
		$bearbeitungsstatusArr = array();
		$this->bearbeitungsstatusRepository->setDefaultOrderings(
				array('name' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $bearbeitungsstatus) {
			if ($bearbeitungsstatus->getName() == 'Online' && $role != 'Flow.Login:Administrator') {
				$bearbeitungsstatusArr[] = '';
			}
			else {
				$bearbeitungsstatusArr[$bearbeitungsstatus->getUUID()] = $bearbeitungsstatus->getName();
			}
		}
		// Personallistenstatus data
		$personallistenstatusArr = array();
		$personallistenstatuses = $this->personallistenstatusRepository->findAll();
		foreach ($personallistenstatuses as $personallistenstatus) {
			$personallistenstatusArr[$personallistenstatus->getUUID()] = $personallistenstatus->getName();
		}
		// Band data
		$bandArr = array();
		$this->bandRepository->setDefaultOrderings(
				array('sortierung' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bands = $this->bandRepository->findAll();
		foreach ($bands as $band) {
			if ($band->getTitel() != 'keine Angabe') {
				$bandNummerTitel = $band->getNummer() . '-' . $band->getTitel();
			}
			else {
				 $bandNummerTitel = $band->getTitel();
			}
			$bandArr[$band->getUUID()] = $bandNummerTitel;
		}

		// Literature data for select box
		$literaturArr = $this->getLiteraturAction();

		// Bistum data for select box
		$bistumArr = array();
		$this->bistumRepository->setDefaultOrderings(
				array('bistum' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bistums = $this->bistumRepository->findAll();
		foreach ($bistums as $bistum) {
			$bistumArr[$bistum->getUUID()] = $bistum->getBistum();
		}
		// Orden data for select box
		$ordenArr = array();
		$this->ordenRepository->setDefaultOrderings(
				array('orden' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$ordens = $this->ordenRepository->findAll();
		foreach ($ordens as $orden) {
			$ordenArr[$orden->getUUID()] = $orden->getOrden();
		}
		// Ordenstyp data for select box
		$ordenstypArr = array();
		$this->ordenstypRepository->setDefaultOrderings(
				array('ordenstyp' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$ordenstyps = $this->ordenstypRepository->findAll();
		foreach ($ordenstyps as $ordenstyp) {
			$ordenstypArr[$ordenstyp->getUUID()] = $ordenstyp->getOrdenstyp();
		}
		// Klosterstatus data for select box
		$klosterstatusArr = array();
		$this->klosterstatusRepository->setDefaultOrderings(
				array('status' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$klosterstatuses = $this->klosterstatusRepository->findAll();
		foreach ($klosterstatuses as $klosterstatus) {
			$klosterstatusArr[$klosterstatus->getUUID()] = $klosterstatus->getStatus();
		}
		// Bearbeiter data for select box
		$bearbeiterArr = array();
		$this->bearbeiterRepository->setDefaultOrderings(
				array('bearbeiter' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$bearbeiters = $this->bearbeiterRepository->findAll();
		foreach ($bearbeiters as $bearbeiter) {
			$bearbeiterArr[$bearbeiter->getUUID()] = $bearbeiter->getBearbeiter();
		}
		// URL-Typ data for select box
		$urltypArr = array();
		$this->urltypRepository->setDefaultOrderings(
				array('name' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$urltyps = $this->urltypRepository->findAll();
		foreach ($urltyps as $urltyp) {
			if ( $urltyp->getName() != 'Wikipedia' && $urltyp->getName() != 'GND' )
				$urltypArr[$urltyp->getUUID()] = $urltyp->getName();
		}
		// Land data for select box
		$landArr = array();
		$this->landRepository->setDefaultOrderings(
				array('land' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$lands = $this->landRepository->findAll();
		foreach ($lands as $land) {
				$landArr[$land->getUUID()] = $land->getLand();
		}
		// Bearbeiter roles
		$roleArr = array();
		foreach ($this->roleRepository->findAll()->toArray() as $role) {
			if (stristr($role->getIdentifier(), 'Flow.Login')) {
				$roleValues = explode(':', $role->getIdentifier());
				$roleArr[$role->getIdentifier()] = $roleValues[1];
			}
		}
		$response = array();
		$response['bearbeitungsstatus'] = $bearbeitungsstatusArr;
		$response['personallistenstatus'] = $personallistenstatusArr;
		$response['band'] = $bandArr;
		$response['literatur'] = $literaturArr;
		$response['bistum'] = $bistumArr;
		$response['orden'] = $ordenArr;
		$response['ordenstyp'] = $ordenstypArr;
		$response['klosterstatus'] = $klosterstatusArr;
		$response['bearbeiter'] = $bearbeiterArr;
		$response['url_typ'] = $urltypArr;
		$response['land'] = $landArr;
		$response['role'] = $roleArr;
		return json_encode($response);
	}

	/** Update a Kloster entity
	 * @return void
	 **/
	public function updateAction() {
		// Update Kloster
		if ($this->request->hasArgument('uUID')) {
			$uuid = $this->request->getArgument('uUID');
		}
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$kloster = $this->klosterRepository->findByIdentifier($uuid);
		if (!is_object($kloster)) {
			$this->throwStatus(400, 'Entity Kloster not available', Null);
		}
		$kloster->setKloster( $this->request->getArgument('kloster_name') );
		$kloster->setPatrozinium( $this->request->getArgument('patrozinium') );
		$kloster->setBemerkung( $this->request->getArgument('bemerkung') );
		$kloster->setBand_seite( $this->request->getArgument('band_seite') );
		$kloster->setText_gs_band( $this->request->getArgument('text_gs_band') );
		$kloster->setBearbeitungsstand( $this->request->getArgument('bearbeitungsstand') );
		$bearbeitungsstatus_uuid = $this->request->getArgument('bearbeitungsstatus');
		$bearbeitungsstatus = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatus_uuid);
		$kloster->setBearbeitungsstatus($bearbeitungsstatus);
		$bearbeiter = $this->bearbeiterObj;
		$kloster->setBearbeiter($bearbeiter);
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
			$klosterstandortArr[$i]['kloster'] = $uuid;
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
			$lastKlosterstandortId = $this->getLastKlosterstandortIdAction();
			foreach ($klosterstandortArr as $ko) {
				$klosterstandort = new Klosterstandort();
				$kloster_uuid = $ko['kloster'];
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$klosterstandort->setUid(++$lastKlosterstandortId);
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
		if (isset($klosterordenArr) && !empty($klosterordenArr) && is_array($klosterordenArr)) {
			$klosterordens = $kloster->getKlosterordens();
			foreach ($klosterordens as $i => $klosterorden) {
				$this->klosterordenRepository->remove($klosterorden);
			}
			$lastKlosterordentId = $this->getLastKlosterordenIdAction();
			foreach ($klosterordenArr as $ko) {
				$klosterorden = new Klosterorden();
				$kloster_uuid = $ko['kloster'];
				$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
				$klosterorden->setUid(++$lastKlosterordentId);
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
						$kloster = $this->klosterRepository->findByIdentifier($uuid);
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
			if ($this->request->hasArgument('gnd_label')) {
				$gnd_label = $this->request->getArgument('gnd_label');
			}
			if (empty($gnd_label)) {
				$gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
				$gnd_label = $kloster . ' [' . $gndid . ']';
			}
			if (isset($gnd) && !empty($gnd)) {
				foreach ($klosterHasUrls as $i => $klosterHasUrl) {
					$urlObj = $klosterHasUrl->getUrl();
					$url = $urlObj->getUrl();
					$urlTypObj = $urlObj->getUrltyp();
					$urlTyp = $urlTypObj->getName();
					if ($urlTyp == "GND") {
						$urlObj->setUrl($gnd);
						if (!empty($gnd_label)) {
							$urlObj->setBemerkung($gnd_label);
						}
						$this->urlRepository->update($urlObj);
						$klosterHasGND = true;
					}
				}
				if (!$klosterHasGND) {
					$url = new Url();
					$url->setUrl($gnd);
					if (!empty($gnd_label)) {
						$url->setBemerkung($gnd_label);
					}
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
			if ($this->request->hasArgument('wikipedia_label')) {
				$wikipedia_label = $this->request->getArgument('wikipedia_label');
			}
			if (empty($wikipedia_label)) {
				$wikipedia_label = str_replace('http://de.wikipedia.org/wiki/', '', trim($wikipedia));
				$wikipedia_label = str_replace('_', ' ', $wikipedia_label);
				$wikipedia_label = rawurldecode($wikipedia_label);
			}
			if (isset($wikipedia) && !empty($wikipedia)) {
				foreach ($klosterHasUrls as $i => $klosterHasUrl) {
					$urlObj = $klosterHasUrl->getUrl();
					$url = $urlObj->getUrl();
					$urlTypObj = $urlObj->getUrltyp();
					$urlTyp = $urlTypObj->getName();
					if ($urlTyp == "Wikipedia") {
						$urlObj->setUrl($wikipedia);
						if (!empty($wikipedia_label)) {
							$urlObj->setBemerkung($wikipedia_label);
						}
						$this->urlRepository->update($urlObj);
						$klosterHasWiki = true;
					}
				}
				if (!$klosterHasWiki) {
					$url = new Url();
					$url->setUrl($wikipedia);
					if (!empty($wikipedia_label)) {
						$url->setBemerkung($wikipedia_label);
					}
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
		// Add Url if set
		if ($this->request->hasArgument('url')) {
			$urlArr = $this->request->getArgument('url');
			if (isset($urlArr) && !empty($urlArr)) {
				if ($this->request->hasArgument('url_typ')) {
					$urlTypArr = $this->request->getArgument('url_typ');
				}
				if ($this->request->hasArgument('links_label')) {
					$linksLabelArr = $this->request->getArgument('links_label');
				}
				if ((isset($urlArr) && !empty($urlArr)) && (isset($urlTypArr) && !empty($urlTypArr))) {
					foreach ($klosterHasUrls as $i => $klosterHasUrl) {
						$urlObj = $klosterHasUrl->getUrl();
						$url = $urlObj->getUrl();
						$urlTypObj = $urlObj->getUrltyp();
						$urlTyp = $urlTypObj->getName();
						if ($urlTyp != "Wikipedia" && $urlTyp != "GND") {
							$this->klosterHasUrlRepository->remove($klosterHasUrl);
							$this->urlRepository->remove($urlObj);
						}
					}
					foreach ($urlArr as $k => $url) {
						if (!empty($url)) {
							$urlObj = new Url();
							$urlObj->setUrl($url);
							if (isset($linksLabelArr[$k]) && !empty($linksLabelArr[$k])) {
								$urlObj->setBemerkung($linksLabelArr[$k]);
							}
							$urlTypObj = $this->urltypRepository->findByIdentifier($urlTypArr[$k]);
							$urlObj->setUrltyp($urlTypObj);
							$this->urlRepository->add($urlObj);
							$klosterhasurlObj = new KlosterHasUrl();
							$klosterhasurlObj->setKloster($kloster);
							$klosterhasurlObj->setUrl($urlObj);
							$this->klosterHasUrlRepository->add($klosterhasurlObj);
						}
					}
				}
			}
		}
		$this->throwStatus(200, NULL, Null);
	}

	/**
	 * Delete a Kloster entity
	 * @return void
	 */
	public function deleteAction() {
		$uuid = $this->request->getArgument('uUID');
		if (empty($uuid)) {
			$this->throwStatus(400, 'Required uUID not provided', Null);
		}
		$kloster = $this->klosterRepository->findByIdentifier($uuid);
		if (!is_object($kloster)) {
			$this->throwStatus(400, 'Entity Kloster not available', Null);
		}
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
		$this->throwStatus(200, NULL, Null);
	}

	/** Updates the list of Kloster
	 * @FLOW\SkipCsrfProtection
	 * @return integer $status http status
	 */
	public function updateListAction() {
		if ($this->request->hasArgument('data')) {
			$klosterlist = $this->request->getArgument('data');
		}
		if (empty($klosterlist)) {
			$this->throwStatus(400, 'Required data arguemnts not provided', Null);
		}
		$uuids = array();
		if (is_array($klosterlist) && !empty($klosterlist)) {
			foreach ($klosterlist as $k => $v) {
				$uuids[] = (string)$k;
				$klosterObject = $this->klosterRepository->findByIdentifier((string)$k);
				$klosterObject->setKloster($v['kloster']);
				$bearbeitungsstatusObject = $this->bearbeitungsstatusRepository->findByIdentifier($v['bearbeitungsstatus']);
				$klosterObject->setBearbeitungsstatus($bearbeitungsstatusObject);
				$klosterObject->setBearbeitungsstand($v['bearbeitungsstand']);
				$this->klosterRepository->update($klosterObject);
				$gndAlreadyExists = False;
				$klosterHasUrls = $klosterObject->getKlosterHasUrls();
				if (is_object($klosterHasUrls) && count($klosterHasUrls) > 0) {
					foreach ($klosterHasUrls as $klosterHasUrl) {
						$urlObject = $klosterHasUrl->getUrl();
						$urlTypObject = $urlObject->getUrltyp();
						$urlTyp = $urlTypObject->getName();
						if ($urlTyp == "GND") {
							if (!empty($v['gnd'])) {
								$urlObject->setUrl($v['gnd']);
								$this->urlRepository->update($urlObject);
							}
							elseif (isset($v['gnd']) && empty($v['gnd'])) {
								$this->klosterHasUrlRepository->remove($klosterHasUrl);
								$this->urlRepository->remove($urlObject);
							}
							$gndAlreadyExists = True;
						}
					}
				}
				if (!$gndAlreadyExists) {
					$urlObject = new Url();
					$urlObject->setUrl($v['gnd']);
					$gndid = str_replace('http://d-nb.info/gnd/', '', $v['gnd']);
					$gndbemerkung = $v['kloster'] . ' [' . $gndid . ']';
					$urlObject->setBemerkung($gndbemerkung);
					$urlTypObj = $this->urltypRepository->findOneByName('GND');
					$urlObject->setUrltyp($urlTypObj);
					$this->urlRepository->add($urlObject);
					$klosterhasurl = new KlosterHasUrl();
					$klosterhasurl->setKloster($klosterObject);
					$klosterhasurl->setUrl($urlObject);
					$this->klosterHasUrlRepository->add($klosterhasurl);
				}
			}
			$this->persistenceManager->persistAll();
		}
		$this->throwStatus(200, NULL, Null);
	}

	/** Gets and returns the list of Orte as per search string
	 * @param void
	 * @return array $reponse
	 */
	public function searchOrtAction() {
		if ($this->request->hasArgument('searchString')) {
			$searchString = trim($this->request->getArgument('searchString'));
			$searchString = "%" . $searchString . "%";
			$searchResult = $this->ortRepository->findOrtBySearchString($searchString);
			$orte = array();
			foreach ($searchResult as $res) {
				$orte[] = array(
					'uUID' => $res->getUUID(),
					'name' => $res->getFullOrt()
				);
			}
			return json_encode($orte);
		}
	}

	/**
	 * @return integer $lastKlosterId The last insert id
	 */
	public function getLastKlosterIdAction() {
		$result = $this->klosterRepository->findLastEntry();
		foreach ($result as $res) {
			$lastKlosterId = $res->getKloster_id();
		}
		return $lastKlosterId;
	}

	/**
	 * @return integer $lastKlosterstandortId The last insert id
	 */
	public function getLastKlosterstandortIdAction() {
		$result = $this->klosterstandortRepository->findLastEntry();
		foreach ($result as $res) {
			$lastKlosterstandortId = $res->getUid();
		}
		return $lastKlosterstandortId;
	}

	/**
	 * @return integer $lastKlosterordenId The last insert id
	 */
	public function getLastKlosterordenIdAction() {
		$result = $this->klosterordenRepository->findLastEntry();
		foreach ($result as $res) {
			$lastKlosterordenId = $res->getUid();
		}
		return $lastKlosterordenId;
	}

	/** Gets and returns a list of Klosters as per search string
	 * @param void
	 * @return array $reponse
	 * @FLOW\SkipCsrfProtection
	 */
	public function searchAction() {
		if ($this->request->hasArgument('alle')) {
			$alle = $this->request->getArgument('alle');
		}
		$searchArr = array();
		if ($this->request->hasArgument('filter')) {
			$filter = $this->request->getArgument('filter');
			if ($this->request->hasArgument('operator')) {
				$operator = $this->request->getArgument('operator');

				if ($this->request->hasArgument('text')) {
					$text = $this->request->getArgument('text');
				}

				if ($this->request->hasArgument('concat')) {
					$concat = $this->request->getArgument('concat');
				}
			}
		}
		if (!empty($filter) && !empty($operator) && !empty($text)) {
			foreach ($filter as $k => $v) {
				$joinParams = $this->joinParamArr[$v];
				if (isset($concat[$k]) && !empty($concat[$k])) {
					$cc = $concat[$k];
				}
				else {
					$cc = null;
				}
				if (trim($v) == 'bundesland.bundesland') {
					$filter = 'land.land';
				}
				else {
					$filter = $v;
				}
				$searchArr[] = array('filter' => $filter, 'operator' => $operator[$k], 'text' => $text[$k], 'joinParams' => $joinParams, 'concat' => $cc);
			}
		}
		if (isset($alle) && !empty($alle)) {
			$searchResult = $this->klosterRepository->findKlosterByWildCard($alle);
			$resultArr = array();
			foreach ($searchResult as $v) {
				$resultArr[] = $v['Persistence_Object_Identifier'];
			}
			return json_encode($resultArr);
		}
		else {
			if (isset($searchArr) && is_array($searchArr)) {
				$searchResult = $this->klosterRepository->findKlosterByAdvancedSearch($searchArr);
				$resultArr = array();
				foreach ($searchResult as $v) {
					$resultArr[] = $v['Persistence_Object_Identifier'];
				}
				return json_encode($resultArr);
			}
		}
	}

	/** Gets and returns the list of Literature key value pairs
	 * @param void
	 * @return array $literaturArr
	 */
	public function getLiteraturAction() {
		$bibliography = $this->proxy->literatureAction();
		$bibliography = json_decode($bibliography, true);
		$literaturArr = array();
		$this->literaturRepository->setDefaultOrderings(
				array('citekey' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);
		$literaturs = $this->literaturRepository->findAll();
		foreach ($literaturs as $q => $literatur) {
			$citekey = $literatur->getCitekey();
			$literatur_beschreibung = $literatur->getBeschreibung();
			$key = array_search($citekey, array_column($bibliography, 'citeid'));
			$literatur_name = '';
			if (!empty($bibliography[$key]['title'])) $literatur_name .= $bibliography[$key]['title'] . ' ';
			if (!empty($bibliography[$key]['editor'])) $literatur_name .= $bibliography[$key]['editor'] . ' ';
			if (!empty($bibliography[$key]['citeid'])) $literatur_name .= $bibliography[$key]['citeid'] . ' ';
			if (!empty($literatur_beschreibung)) $literatur_name .= "(" . $literatur_beschreibung . ")";
			if (!empty($bibliography[$key]['note'])) $literatur_name .= $bibliography[$key]['note'];
			$literaturArr[$literatur->getUUID()] = $literatur_name;
		}
		return $literaturArr;
	}

}
?>