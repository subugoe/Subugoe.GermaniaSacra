<?php
namespace Subugoe\GermaniaSacra\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Subugoe.GermaniaSacra". *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Subugoe\GermaniaSacra\Domain\Model\Kloster;
use Subugoe\GermaniaSacra\Domain\Model\Klosterstandort;
use Subugoe\GermaniaSacra\Domain\Model\Klosterorden;

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
	 * @return void
	 */
	public function jsonListAction() {
		$this->klosterRepository->setDefaultOrderings(
			array( 'uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		$klosters = $this->klosterRepository->findKlosters();

		$klosterArr = array();
		foreach ($klosters as $k => $kloster) {
			$klosterArr[$k]['uuid'] = $kloster->getUUID();
			$klosterArr[$k]['uid'] = $kloster->getUid();
			$klosterArr[$k]['kloster'] = $kloster->getKloster();
			$klosterArr[$k]['kloster_id'] = $kloster->getKloster_id();

			$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
			$klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus->getUUID();
			$klosterstandorts = $kloster->getKlosterstandorts();
			foreach ($klosterstandorts as $i => $klosterstandort) {
				$ort = $klosterstandort->getOrt();
				$klosterArr[$k]['ort'][$i] = $ort->getUUID();
			}

			$klosterHasUrls = $kloster->getKlosterHasUrls();
			foreach ($klosterHasUrls as $i => $klosterHasUrl) {
				$urlObj = $klosterHasUrl->getUrl();
				$url = $urlObj->getUrl();
				$urlTypObj = $urlObj->getUrltyp();
				$urlTyp = $urlTypObj->getName();

				if ($urlTyp == "GND") {
					$klosterArr[$k]['GND'] = $url;
				}
			}
		}

		$ortGemeindeKreisArr = array();
		$orte = $this->ortRepository->findAll();
		foreach ($orte as $l=>$ort) {
			$ortGemeindeKreisArr[$l] = array($ort->getOrtGemeindeKreis() => $ort->getUUID());
		}

		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n=>$bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		$response = array();
		$response[] = $klosterArr;
		$response[] = $ortGemeindeKreisArr;
		$response[] = $bearbeitungsstatusArr;

		return json_encode($response);
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('klosters', $this->klosterRepository->findAll());
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function showAction(Kloster $kloster) {
		$this->view->assign('kloster', $kloster);
	}

	/**
	 * @return void
	 */
	public function newAction() {

		$orte = $this->ortRepository->findAll();
		$ortGemeindeKreisArr = array();
		foreach ($orte as $ort) {
			$ortGemeindeKreisArr[] = array ($ort->getOrtGemeindeKreis() => $ort->getUUID());
		}

		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n=>$bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		$personallistenstatusArr = array();
		$personallistenstatuses = $this->personallistenstatusRepository->findAll();
		foreach ($personallistenstatuses as $m=>$personallistenstatus) {
			$personallistenstatusArr[$m] = array($personallistenstatus->getName() => $personallistenstatus->getUUID());
		}

		$bandArr = array();
		$bands = $this->bandRepository->findAll();
		foreach ($bands as $p=>$band) {
			$bandArr[$p] = array($band->getTitel() => $band->getUUID());
		}

		$literaturArr = array();
		$literaturs = $this->literaturRepository->findAll();
		foreach ($literaturs as $q=>$literatur) {
			$literaturArr[$q] = array($literatur->getCitekey() => $literatur->getUUID());
		}

		$bistumArr = array();
		$bistums = $this->bistumRepository->findAll();
		foreach ($bistums as $r=>$bistum) {
			$bistumArr[$r] = array($bistum->getBistum() => $bistum->getUUID());
		}

		$ordenArr = array();
		$ordens = $this->ordenRepository->findAll();
		foreach ($ordens as $m=>$orden) {
			$ordenArr[$m] = array($orden->getOrden() => $orden->getUUID());
		}

		$klosterstatusArr = array();
		$klosterstatuses = $this->klosterstatusRepository->findAll();
		foreach ($klosterstatuses as $n=>$klosterstatus) {
			$klosterstatusArr[$n] = array($klosterstatus->getStatus() => $klosterstatus->getUUID());
		}

		$zeitraumArr = array();
		$zeitraums = $this->zeitraumRepository->findAll();
		foreach ($zeitraums as $k=>$zeitraum) {
			$zr = $zeitraum->getVon_von() . " -> " . $zeitraum->getVon_bis() . " (" . $zeitraum->getVon_verbal() . ")";
			$zr .= " - " . $zeitraum->getBis_von() . " -> " . $zeitraum->getBis_bis() . " (" . $zeitraum->getBis_verbal() . ")";
			$zeitraumArr[$k] = array($zr => $zeitraum->getUUID());
		}

		$response = array();
		$response[] = $ortGemeindeKreisArr;
		$response[] = $bearbeitungsstatusArr;
		$response[] = $personallistenstatusArr;
		$response[] = $bandArr;
		$response[] = $literaturArr;
		$response[] = $bistumArr;
		$response[] = $ordenArr;
		$response[] = $klosterstatusArr;
		$response[] = $zeitraumArr;

		return json_encode($response);

	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function createAction() {

		$kloster = new Kloster();

		//Kloster
		$kloster_name = $this->request->getArgument('new_kloster_name');
		$patrozinium = $this->request->getArgument('new_patrozinium');
		$bemerkung = $this->request->getArgument('new_bemerkung');
		$band_seite = $this->request->getArgument('new_band_seite');
		$text_gs_band = $this->request->getArgument('new_text_gs_band');
		$kloster->setKloster($kloster_name);
		$kloster->setPatrozinium($patrozinium);
		$kloster->setBemerkung($bemerkung);
		$kloster->setBand_seite($band_seite);
		$kloster->setText_gs_band($text_gs_band);

		$bearbeitungsstatus_uuid = $this->request->getArgument('new_bearbeitungsstatus');
		$bearbeitungsstatus = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatus_uuid);
		$kloster->setBearbeitungsstatus($bearbeitungsstatus);
		$personallistenstatus_uuid = $this->request->getArgument('new_personallistenstatus');
		$personallistenstatus = $this->personallistenstatusRepository->findByIdentifier($personallistenstatus_uuid);
		$kloster->setPersonallistenstatus($personallistenstatus);
		$band_uuid = $this->request->getArgument('new_band');

		if (isset($band_uuid) && !empty($band_uuid)) {
			$band = $this->bandRepository->findByIdentifier($band_uuid);
			$kloster->setBand($band);
		}

		$this->klosterRepository->add($kloster);

		$id = $kloster->getUUID();

		// Klosterstandort
		$ortArr = $this->request->getArgument('new_ort');
		$gruenderArr = $this->request->getArgument('new_gruender');
		$breiteArr = $this->request->getArgument('new_breite');
		$laengeArr = $this->request->getArgument('new_laenge');
		$bemerkungArr = $this->request->getArgument('new_standortbemerkung');
		$bemerkung_standortArr = $this->request->getArgument('new_bemerkung_standort');
		$temp_literatur_altArr = $this->request->getArgument('new_temp_literatur_alt');
		$klosterstandort_zeitraumArr = $this->request->getArgument('new_klosterstandort_zeitraum');
		$klosterstandortNumber = count($ortArr);
		$klosterstandortArr = array();
		for ($i=0; $i<$klosterstandortNumber; $i++) {
			$klosterstandortArr[$i]['kloster'] = $id;
			$klosterstandortArr[$i]['ort'] = $ortArr[$i];
			$klosterstandortArr[$i]['gruender'] = $gruenderArr[$i];
			$klosterstandortArr[$i]['breite'] = $breiteArr[$i];
			$klosterstandortArr[$i]['laenge'] = $laengeArr[$i];
			$klosterstandortArr[$i]['bemerkung'] = $bemerkungArr[$i];
			$klosterstandortArr[$i]['bemerkung_standort'] = $bemerkung_standortArr[$i];
			$klosterstandortArr[$i]['temp_literatur_alt'] = $temp_literatur_altArr[$i];
			$klosterstandortArr[$i]['klosterstandort_zeitraum'] = $klosterstandort_zeitraumArr[$i];
		}
		$klosterstandort = new Klosterstandort();
		foreach ($klosterstandortArr as $ko) {
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
			$zeitraum_uuid = $ko['klosterstandort_zeitraum'];
			$zeitraum = $this->zeitraumRepository->findByIdentifier($zeitraum_uuid);
			$klosterstandort->setZeitraum($zeitraum);
			$this->klosterstandortRepository->add($klosterstandort);
		}

		// Orden
		$ordenArr = $this->request->getArgument('new_orden');
		$orden_zeitraumArr = $this->request->getArgument('new_orden_zeitraum');
		$klosterstatusArr = $this->request->getArgument('new_klosterstatus');
		$bemerkung_ordenArr = $this->request->getArgument('new_bemerkung_orden');
		$klosterordenNumber = count($ordenArr);
		$klosterordenArr = array();
		for ($i=0; $i<$klosterordenNumber; $i++) {
			$klosterordenArr[$i]['kloster'] = $id;
			$klosterordenArr[$i]['orden'] = $ordenArr[$i];
			$klosterordenArr[$i]['orden_zeitraum'] = $orden_zeitraumArr[$i];
			$klosterordenArr[$i]['klosterstatus'] = $klosterstatusArr[$i];
			$klosterordenArr[$i]['bemerkung_orden'] = $bemerkung_ordenArr[$i];
		}
		$klosterorden = new Klosterorden();
		foreach ($klosterordenArr as $ko) {
			$kloster_uuid = $ko['kloster'];
			$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
			$klosterorden->setKloster($kloster);
			$zeitraum_uuid = $ko['orden_zeitraum'];
			$zeitraum = $this->zeitraumRepository->findByIdentifier($zeitraum_uuid);
			$klosterorden->setZeitraum($zeitraum);
			$orden_uuid = $ko['orden'];
			$orden = $this->ordenRepository->findByIdentifier($orden_uuid);
			$klosterorden->setOrden($orden);
			$klosterstatus_uuid = $ko['klosterstatus'];
			$klosterstatus = $this->klosterstatusRepository->findByIdentifier($klosterstatus_uuid);
			$klosterorden->setKlosterstatus($klosterstatus);
			$klosterorden->setBemerkung($ko['bemerkung_orden']);
			$this->klosterordenRepository->add($klosterorden);
		}

		return json_encode(array(201));
	}


	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function editAction(Kloster $kloster) {

		$klosterArr = array();
		$klosterArr['uuid'] = $kloster->getUUID();
		$klosterArr['uid'] = $kloster->getUid();
		$klosterArr['kloster_name'] = $kloster->getKloster();
		$klosterArr['kloster_id'] = $kloster->getKloster_id();
		$klosterArr['patrozinium'] = $kloster->getPatrozinium();
		$klosterArr['bemerkung'] = $kloster->getBemerkung();
		$klosterArr['band_seite'] = $kloster->getBand_seite();
		$klosterArr['text_gs_band'] = $kloster->getText_gs_band();

		$band = $kloster->getBand();
		if (is_object($band)) {
			$klosterArr['band'] = $band->getUUID();
		}

		$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
		$klosterArr['bearbeitungsstatus'] = $bearbeitungsstatus->getUUID();

		$personallistenstatus = $kloster->getPersonallistenstatus();
		$klosterArr['personallistenstatus'] = $personallistenstatus->getUUID();

		$klosterstandorte = array();
		$klosterstandorts = $kloster->getKlosterstandorts();
		foreach ($klosterstandorts as $i => $klosterstandort) {

			$klosterstandorte[$i]['breite'] = $klosterstandort->getBreite();
			$klosterstandorte[$i]['laenge'] = $klosterstandort->getLaenge();
			$klosterstandorte[$i]['gruender'] = $klosterstandort->getGruender();
			$klosterstandorte[$i]['bemerkung_standort'] = $klosterstandort->getBemerkung_standort();

			$ort = $klosterstandort->getOrt();
			$klosterstandorte[$i]['ort'] = $ort->getUUID();
			$klosterstandorte[$i]['wuestung'] = $ort->getWuestung();

			$bistumUUID = $ort->getBistum();
			$klosterstandorte[$i]['bistum'] = $bistumUUID->getUUID();

			$zeitraum = $klosterstandort->getZeitraum();

			if (is_object($zeitraum)) {
				$klosterstandorte[$i]['klosterstandort_zeitraum'] = $zeitraum->getUUID();
				$klosterstandorte[$i]['von_von'] = $zeitraum->getVon_von();
				$klosterstandorte[$i]['von_bis'] = $zeitraum->getVon_bis();
				$klosterstandorte[$i]['von_verbal'] = $zeitraum->getVon_verbal();
				$klosterstandorte[$i]['bis_von'] = $zeitraum->getBis_von();
				$klosterstandorte[$i]['bis_bis'] = $zeitraum->getBis_bis();
				$klosterstandorte[$i]['bis_verbal'] = $zeitraum->getBis_verbal();
			}
		}
		$klosterArr['klosterstandorte'] = $klosterstandorte;

		$klosterorden = array();
		$klosterordens = $kloster->getKlosterordens();
		foreach ($klosterordens as $i => $ko) {
			$klosterorden[$i]['bemerkung_orden'] = $ko->getBemerkung();

			$orden = $ko->getOrden();
			$klosterorden[$i]['orden'] = $orden->getUUID();

			$klosterstatus = $ko->getKlosterstatus();
			$klosterorden[$i]['klosterstatus'] = $klosterstatus->getUUID();

			$zeitraum = $ko->getZeitraum();
			$klosterorden[$i]['orden_zeitraum'] = $zeitraum->getUUID();
			$klosterorden[$i]['orden_von_von'] = $zeitraum->getVon_von();
			$klosterorden[$i]['orden_von_bis'] = $zeitraum->getVon_bis();
			$klosterorden[$i]['orden_von_verbal'] = $zeitraum->getVon_verbal();
			$klosterorden[$i]['orden_bis_von'] = $zeitraum->getBis_von();
			$klosterorden[$i]['orden_bis_bis'] = $zeitraum->getBis_bis();
			$klosterorden[$i]['orden_bis_verbal'] = $zeitraum->getBis_verbal();
		}
		$klosterArr['klosterorden'] = $klosterorden;

		$Urls = array();
		$klosterHasUrls = $kloster->getKlosterHasUrls();
		foreach ($klosterHasUrls as $i => $klosterHasUrl) {
			$urlObj = $klosterHasUrl->getUrl();
			$url = $urlObj->getUrl();
			$urlTypObj = $urlObj->getUrltyp();
			$urlTyp = $urlTypObj->getName();
			$Urls[$i] = array($urlTyp => $url);
		}
		$klosterArr['url'] = $Urls;

		$Literaturs = array();
		$klosterHasLiteraturs = $kloster->getKlosterHasLiteraturs();
		foreach ($klosterHasLiteraturs as $i => $klosterHasLiteratur) {
			$literaturObj = $klosterHasLiteratur->getLiteratur();
			$literatur = $literaturObj->getUUID();
			$Literaturs[$i] = $literatur;
		}
		$klosterArr['literatur'] = $Literaturs;

		$bearbeitungsstatusArr = array();
		$bearbeitungsstatuses = $this->bearbeitungsstatusRepository->findAll();
		foreach ($bearbeitungsstatuses as $n=>$bearbeitungsstatus) {
			$bearbeitungsstatusArr[$n] = array($bearbeitungsstatus->getName() => $bearbeitungsstatus->getUUID());
		}

		$personallistenstatusArr = array();
		$personallistenstatuses = $this->personallistenstatusRepository->findAll();
		foreach ($personallistenstatuses as $m=>$personallistenstatus) {
			$personallistenstatusArr[$m] = array($personallistenstatus->getName() => $personallistenstatus->getUUID());
		}

		$bandArr = array();
		$bands = $this->bandRepository->findAll();
		foreach ($bands as $p=>$band) {
			$bandArr[$p] = array($band->getTitel() => $band->getUUID());
		}

		$literaturArr = array();
		$literaturs = $this->literaturRepository->findAll();
		foreach ($literaturs as $q=>$literatur) {
			$literaturArr[$q] = array($literatur->getCitekey() => $literatur->getUUID());
		}

		$bistumArr = array();
		$bistums = $this->bistumRepository->findAll();
		foreach ($bistums as $r=>$bistum) {
			$bistumArr[$r] = array($bistum->getBistum() => $bistum->getUUID());
		}

		$ordenArr = array();
		$ordens = $this->ordenRepository->findAll();
		foreach ($ordens as $m=>$orden) {
			$ordenArr[$m] = array($orden->getOrden() => $orden->getUUID());
		}

		$klosterstatusArr = array();
		$klosterstatuses = $this->klosterstatusRepository->findAll();
		foreach ($klosterstatuses as $n=>$klosterstatus) {
			$klosterstatusArr[$n] = array($klosterstatus->getStatus() => $klosterstatus->getUUID());
		}

		$zeitraumArr = array();
		$zeitraums = $this->zeitraumRepository->findAll();
		foreach ($zeitraums as $k=>$zeitraum) {
			$zr = $zeitraum->getVon_von() . " -> " . $zeitraum->getVon_bis() . " (" . $zeitraum->getVon_verbal() . ")";
			$zr .= " - " . $zeitraum->getBis_von() . " -> " . $zeitraum->getBis_bis() . " (" . $zeitraum->getBis_verbal() . ")";
			$zeitraumArr[$k] = array($zr => $zeitraum->getUUID());
		}

		$response = array();
		$response[] = $klosterArr;
		$response[] = $bearbeitungsstatusArr;
		$response[] = $personallistenstatusArr;
		$response[] = $bandArr;
		$response[] = $literaturArr;
		$response[] = $bistumArr;
		$response[] = $ordenArr;
		$response[] = $klosterstatusArr;
		$response[] = $zeitraumArr;

		return json_encode($response);
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 * @FLOW\SkipCsrfProtection
	 **/
	public function updateAction(Kloster $kloster) {

		$param = $this->request->getArguments();
		$id = $param['kloster']['__identity'];

		$kloster = $this->klosterRepository->findByIdentifier($id);

		// Kloster
		$kloster_name = $this->request->getArgument('kloster_name');
		$patrozinium = $this->request->getArgument('patrozinium');
		$bemerkung = $this->request->getArgument('bemerkung');
		$band_seite = $this->request->getArgument('band_seite');
		$text_gs_band = $this->request->getArgument('text_gs_band');
		$kloster->setKloster($kloster_name);
		$kloster->setPatrozinium($patrozinium);
		$kloster->setBemerkung($bemerkung);
		$kloster->setBand_seite($band_seite);
		$kloster->setText_gs_band($text_gs_band);
		$bearbeitungsstatus_uuid = $this->request->getArgument('bearbeitungsstatus');
		$bearbeitungsstatus = $this->bearbeitungsstatusRepository->findByIdentifier($bearbeitungsstatus_uuid);
		$kloster->setBearbeitungsstatus($bearbeitungsstatus);
		$personallistenstatus_uuid = $this->request->getArgument('personallistenstatus');
		$personallistenstatus = $this->personallistenstatusRepository->findByIdentifier($personallistenstatus_uuid);
		$kloster->setPersonallistenstatus($personallistenstatus);
		$band_uuid = $this->request->getArgument('band');
		$band = $this->bandRepository->findByIdentifier($band_uuid);
		$kloster->setBand($band);
		$this->klosterRepository->update($kloster);

		// Klosterstandort
		$ortArr = $this->request->getArgument('ort');
		$gruenderArr = $this->request->getArgument('gruender');
		$breiteArr = $this->request->getArgument('breite');
		$laengeArr = $this->request->getArgument('laenge');
		$bemerkung_standortArr = $this->request->getArgument('bemerkung_standort');
		$klosterstandort_zeitraumArr = $this->request->getArgument('klosterstandort_zeitraum');
		$klosterstandortNumber = count($ortArr);
		$klosterstandortArr = array();
		for ($i=0; $i<$klosterstandortNumber; $i++) {
			$klosterstandortArr[$i]['kloster'] = $id;
			$klosterstandortArr[$i]['ort'] = $ortArr[$i];
			$klosterstandortArr[$i]['gruender'] = $gruenderArr[$i];
			$klosterstandortArr[$i]['breite'] = $breiteArr[$i];
			$klosterstandortArr[$i]['laenge'] = $laengeArr[$i];
			$klosterstandortArr[$i]['bemerkung_standort'] = $bemerkung_standortArr[$i];
			$klosterstandortArr[$i]['klosterstandort_zeitraum'] = $klosterstandort_zeitraumArr[$i];
		}
		$klosterstandorts = $kloster->getKlosterstandorts();
		foreach ($klosterstandorts as $i => $klosterstandort) {
			$this->klosterstandortRepository->remove($klosterstandort);
		}
		$klosterstandort = new Klosterstandort();
		foreach ($klosterstandortArr as $ko) {
			$kloster_uuid = $ko['kloster'];
			$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
			$klosterstandort->setKloster($kloster);
			$ort_uuid = $ko['ort'];
			$ort = $this->ortRepository->findByIdentifier($ort_uuid);
			$klosterstandort->setOrt($ort);
			$klosterstandort->setGruender($ko['gruender']);
			$klosterstandort->setBreite($ko['breite']);
			$klosterstandort->setLaenge($ko['laenge']);
			$zeitraum_uuid = $ko['klosterstandort_zeitraum'];
			$zeitraum = $this->zeitraumRepository->findByIdentifier($zeitraum_uuid);
			$klosterstandort->setZeitraum($zeitraum);
			$this->klosterstandortRepository->add($klosterstandort);
		}

		// Orden
		$ordenArr = $this->request->getArgument('orden');
		$orden_zeitraumArr = $this->request->getArgument('orden_zeitraum');
		$klosterstatusArr = $this->request->getArgument('klosterstatus');
		$bemerkung_ordenArr = $this->request->getArgument('bemerkung_orden');
		$klosterordenNumber = count($ordenArr);
		$klosterordenArr = array();
		for ($i=0; $i<$klosterordenNumber; $i++) {
			$klosterordenArr[$i]['kloster'] = $id;
			$klosterordenArr[$i]['orden'] = $ordenArr[$i];
			$klosterordenArr[$i]['orden_zeitraum'] = $orden_zeitraumArr[$i];
			$klosterordenArr[$i]['klosterstatus'] = $klosterstatusArr[$i];
			$klosterordenArr[$i]['bemerkung_orden'] = $bemerkung_ordenArr[$i];
		}
		$klosterordens = $kloster->getKlosterordens();
		foreach ($klosterordens as $i => $klosterorden) {
			$this->klosterordenRepository->remove($klosterorden);
		}
		$klosterorden = new Klosterorden();
		foreach ($klosterordenArr as $ko) {
			$kloster_uuid = $ko['kloster'];
			$kloster = $this->klosterRepository->findByIdentifier($kloster_uuid);
			$klosterorden->setKloster($kloster);
			$zeitraum_uuid = $ko['orden_zeitraum'];
			$zeitraum = $this->zeitraumRepository->findByIdentifier($zeitraum_uuid);
			$klosterorden->setZeitraum($zeitraum);
			$orden_uuid = $ko['orden'];
			$orden = $this->ordenRepository->findByIdentifier($orden_uuid);
			$klosterorden->setOrden($orden);
			$klosterstatus_uuid = $ko['klosterstatus'];
			$klosterstatus = $this->klosterstatusRepository->findByIdentifier($klosterstatus_uuid);
			$klosterorden->setKlosterstatus($klosterstatus);
			$klosterorden->setBemerkung($ko['bemerkung_orden']);
			$this->klosterordenRepository->add($klosterorden);
		}

//		$bistumArr = $this->request->getArgument('bistum');
//		$bistum_uuid = $bistumArr[$i];
//		$bistum = $this->bistumRepository->findByIdentifier($bistum_uuid);
//		$klosterstandort->setBistum($bistum);

//		$this->klosterstandortRepository->update($klosterstandort);

		$status = 200;
		return json_encode(array($status));

	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function deleteAction(Kloster $kloster) {
		$this->klosterRepository->remove($kloster);
		$this->addFlashMessage('Deleted a kloster.');
		$this->redirect('index');
	}

	public function generateUUIDAction() {
		$i = 1;
		while ($i <= 11):
			$UUID = \TYPO3\Flow\Utility\Algorithms::generateUUID();
			if (isset($LastUUID) && $UUID != $LastUUID) {
				echo $UUID . "<br><br>";
//				echo "'" . $UUID ."'," . "<br><br>";
			}
			$LastUUID = $UUID;
		    $i++;
		endwhile;

		die;
	}
}

?>