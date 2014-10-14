<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

ini_set('memory_limit', '-1');

class DataExportController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

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
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\OrdenRepository
	 */
	protected $ordenRepository;

	/**
	 * @var \Solarium\Client
	 */
	protected $client;

	/**
	 * Solr configuration Array
	 * @var array
	 */
	protected $configuration = array();

	const DISTANT_PAST = -10000;
	const DISTANT_FUTURE = 10000;
	const MIN_YEAR = 700;
	const MAX_YEAR = 1810;
	const YEAR_STEP = 10;

	const PERSONEN_URL = 'http://personendatenbank.germania-sacra.de/export/export.json';

	/**
	 * @var mixed
	 */
	protected $personen;

	/**
	 * @var \TYPO3\Flow\Log\Logger
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	public function __construct($logger = NULL) {
		parent::__construct();
		$this->logger = $logger;
	}

	public function initializeAction() {
		$this->configuration = array(
				'endpoint' => array(
						'localhost' => array(
								'host' => $this->settings['solr']['host'],
								'port' => $this->settings['solr']['port'],
								'path' => $this->settings['solr']['path'],
								'core' => $this->settings['solr']['core'],
								'timeout' => $this->settings['solr']['timeout']
						)
				),
		);

		if (!$this->logger) {
			$log = new \TYPO3\Flow\Log\LoggerFactory();

			$this->logger = $log->create(
					'GermaniaSacra',
					'TYPO3\Flow\Log\Logger',
					'\TYPO3\Flow\Log\Backend\FileBackend',
					array(
							'logFileUrl' => FLOW_PATH_DATA . 'Logs/GermaniaSacra/Mysql2Solr.log',
							'createParentDirectories' => TRUE
					)
			);
		}

		$this->client = new \Solarium\Client($this->configuration);
		$this->client->setAdapter('Solarium\Core\Client\Adapter\Http');

		$personenFile = FLOW_PATH_DATA . 'GermaniaSacra/personen.json';

		try {
			file_put_contents($personenFile, fopen(self::PERSONEN_URL, 'r'));
		} catch (\Exception $e) {
			$this->logger->logException($e);
		}

		$this->personen = json_decode(file_get_contents($personenFile), true);

	}

	public function deleteAction() {
		// get an update query instance
		$update = $this->client->createUpdate();

		// add the delete query and a commit command to the update query
		$update->addDeleteQuery('*:*');
		$update->addCommit();

		// this executes the query and returns the result
		$result = $this->client->update($update);
	}

	/**
	 * Exports Kloster data from mysql into solr
	 */

	public function mysql2solrExportAction() {

		$klosterData = $this->klosterListAllAction();

		$klosterArr = $klosterData[0];
		$klosterstandortArr = $klosterData[1];
		$klosterordenArr = $klosterData[2];
		$standort_ordenArr = $klosterData[3];

		$this->deleteAction();
		$update = $this->client->createUpdate();
		$docs = array();

		foreach ($klosterArr as $k => $v) {
			$doc = $update->createDocument();
			foreach ($v as $i => $v1) {
				$doc->$i = $v1;
			}
			array_push($docs, $doc);
		}

		foreach ($klosterstandortArr as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$doc = $update->createDocument();
				foreach ($v1 as $k2 => $v2) {
					$doc->$k2 = $v2;
				}
				array_push($docs, $doc);
			}
		}

		foreach ($klosterordenArr as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$doc = $update->createDocument();
				foreach ($v1 as $k2 => $v2) {
					$doc->$k2 = $v2;
				}
				array_push($docs, $doc);
			}
		}

		foreach ($standort_ordenArr as $k => $v) {
			foreach ($v as $k1 => $v1) {
				foreach ($v1 as $k2 => $v2) {
					$doc = $update->createDocument();
					foreach ($v2 as $k3 => $v3) {
						$doc->$k3 = $v3;
					}
					array_push($docs, $doc);
				}
			}
		}

		$update->addDocuments($docs);
		$update->addCommit();
		$result = $this->client->update($update);

		$this->logger->log('Data export completed in ' . round($result->getQueryTime() / 100) . ' seconds.');

		return;
	}

	/**
	 * @return array $reponse The list of all Kloster in json format
	 */
	public function klosterListAllAction() {

		if ($this->personen === Null) {
			$this->logger->log('Personendatenbank ist nicht verfügbar.');
			exit;
		}

		$this->klosterRepository->setDefaultOrderings(
				array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		$klosters = $this->klosterRepository->findAll();

		if (!empty($klosters)) {

			$klosterArr = array();
			$klosterstandorte = array();
			$klosterorden = array();
			$standort_ordenArr = array();

			foreach ($klosters as $k => $kloster) {
				$sql_uid = $kloster->getUid();
				$klosterArr[$k]['sql_uid'] = $sql_uid;
				$kloster_id = $kloster->getKloster_id();
				$klosterArr[$k]['kloster_id'] = $kloster_id;
				$klosterName = $kloster->getKloster();
				$klosterArr[$k]['kloster'] = $klosterName;
				$patrozinium = $kloster->getPatrozinium();
				$klosterArr[$k]['patrozinium'] = $patrozinium;
				$bemerkung_kloster = $kloster->getBemerkung();
				$klosterArr[$k]['bemerkung_kloster'] = $bemerkung_kloster;

				$text_gs_band = $kloster->getText_gs_band();
				$klosterArr[$k]['text_gs_band'] = $text_gs_band;

				$band_seite = $kloster->getBand_seite();
				$klosterArr[$k]['band_seite'] = $band_seite;

				$bearbeitungsstatusObj = $kloster->getBearbeitungsstatus();
				$bearbeitungsstatus = $bearbeitungsstatusObj->getName();
				$klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus;

				$personallistenstatusObj = $kloster->getPersonallistenstatus();
				$personallistenstatus = $personallistenstatusObj->getName();
				$klosterArr[$k]['personallistenstatus'] = $personallistenstatus;

				$klosterArr[$k]['typ'] = 'kloster';
				$klosterArr[$k]['id'] = (string)$kloster->getKloster_id();

				$band = $kloster->getBand();
				if (is_object($band) && $band->getNummer() !== 'keine Angabe') {
					$klosterArr[$k]['band_id'] = $band->getUid();
					$klosterArr[$k]['band_nummer'] = $band->getNummer();
					$klosterArr[$k]['band_titel'] = $band->getTitel();
					$klosterArr[$k]['band_kurztitel'] = $band->getKurztitel();
					$klosterArr[$k]['band_sortierung'] = $band->getSortierung();
					$bandSortName = str_pad($band->getSortierung(), 4, "0", STR_PAD_LEFT) . '####' . $band->getNummer() . ' ' . $band->getKurztitel();
					$klosterArr[$k]['band_facet'][] = $bandSortName;
					$klosterArr[$k]['band_facet'][] = 'hat_band';
					$band_facet = $klosterArr[$k]['band_facet'];

					$bandHasUrls = $band->getBandHasUrls();
					foreach ($bandHasUrls as $bandHasUrl) {
						$urlObj = $bandHasUrl->getUrl();
						$bandUrl = $urlObj->getUrl();
						$urlTypObj = $urlObj->getUrltyp();
						$urlTyp = trim($urlTypObj->getName());

						if ($urlTyp == "Handle") {
							$klosterArr[$k]['band_url'] = $bandUrl;
						}

						if ($urlTyp == "Findpage") {
							$klosterArr[$k]['band_url_seitengenau'] = $bandUrl;
						}
					}
				}

				$klosterHasUrls = $kloster->getKlosterHasUrls();

				if (isset($klosterHasUrls) && !empty($klosterHasUrls)) {
					foreach ($klosterHasUrls as $klosterHasUrl) {
						$urlObj = $klosterHasUrl->getUrl();
						$klosterUrl = $urlObj->getUrl();

						if ($klosterUrl !== 'keine Angabe') {
							$urlTypObj = $urlObj->getUrltyp();
							$urlTyp = $urlTypObj->getName();
							if ($urlTyp == "Wikipedia") {
								$url_wikipedia = rawurldecode($klosterUrl);
								$klosterArr[$k]['url_wikipedia'] = $url_wikipedia;
							} elseif ($urlTyp == "Quelle") {
								$url_quelle = rawurldecode($klosterUrl);
								$klosterArr[$k]['url_quelle'] = $url_quelle;

								$url_quelle_titel = $urlObj->getBemerkung();
								$klosterArr[$k]['url_quelle_titel'] = $url_quelle_titel;
							} else {
								$url = rawurldecode($klosterUrl);
								$klosterArr[$k]['url'] = $url;

								$url_bemerkung = $urlObj->getBemerkung();
								$klosterArr[$k]['url_bemerkung'] = $url_bemerkung;
								$klosterArr[$k]['url_typ'] = $urlTyp;
								$url_relation = 'kloster';
								$klosterArr[$k]['url_relation'] = $url_relation;
							}

							if ($urlTyp == "GND") {
								$components = explode("/gnd/", $klosterUrl);
								if (count($components) > 1) {
									$gnd = $components[1];
									$klosterArr[$k]['gnd'] = $gnd;
								}
							}
						}
					}
				}

				$klosterHasLiteraturs = $kloster->getKlosterHasLiteraturs();
				$citekey = array();
				$beschreibung = array();
				foreach ($klosterHasLiteraturs as $l => $klosterHasLiteratur) {
					$literaturObj = $klosterHasLiteratur->getLiteratur();
					$ck = $literaturObj->getCitekey();
					if (!empty($ck)) {
						$citekey[] = $ck;
						$klosterArr[$k]['literatur_citekey'][$l] = $ck;
					} else {
						$klosterArr[$k]['literatur_citekey'][$l] = '';
					}
					$be = $literaturObj->getBeschreibung();
					if (!empty($be)) {
						$beschreibung[] = $be;
						$klosterArr[$k]['literatur_beschreibung'][$l] = $be;
					} else {
						$klosterArr[$k]['literatur_beschreibung'][$l] = '';
					}
				}

				$standortuidArr = array();
				$koordinatenArr = array();
				$koordinaten_institutionengenauArr = array();
				$gruenderArr = array();
				$von_vonArr = array();
				$von_bisArr = array();
				$von_verbalArr = array();
				$vonArr = array();
				$bis_vonArr = array();
				$bis_bisArr = array();
				$bis_verbalArr = array();
				$bisArr = array();
				$ortArr = array();
				$ortuidArr = array();
				$wuestungArr = array();
				$landArr = array();
				$ist_in_deutschlandArr = array();
				$bistumuidArr = array();
				$bistumArr = array();
				$kirchenprovinzArr = array();
				$ist_erzbistumArr = array();

				$bemerkung_standortArr = array();

				$kloster_standort_jahr50 = array();
				$start = self::MIN_YEAR;

				$klosterstandorts = $kloster->getKlosterstandorts();
				foreach ($klosterstandorts as $i => $klosterstandort) {

					$ortObj = $klosterstandort->getOrt();
					if (is_object($ortObj)) {
						$standortuid = $klosterstandort->getUid();
						if (!empty($standortuid)) {
							$standortuidArr[] = $standortuid;
							$klosterstandorte[$k][$i]['id'] = 'kloster-standort-' . $standortuid;
						}

						$klosterstandorte[$k][$i]['sql_uid'] = $kloster_id;

						$klosterstandorte[$k][$i]['kloster_id'] = $kloster_id;
						$klosterstandorte[$k][$i]['typ'] = 'kloster-standort';

						$breite = $klosterstandort->getBreite();
						$laenge = $klosterstandort->getLaenge();
						if (!empty($breite) && !empty($laenge)) {
							$koordinatenArr[] = $breite . "," . $laenge;
							$koordinaten_institutionengenauArr[] = True;
							$klosterstandorte[$k][$i]['koordinaten_institutionengenau'] = True;
						} else {
							$ortObj = $klosterstandort->getOrt();
							$breite = $ortObj->getBreite();
							$laenge = $ortObj->getLaenge();
							$koordinatenArr[] = $breite . "," . $laenge;
							$koordinaten_institutionengenauArr[] = False;
							$klosterstandorte[$k][$i]['koordinaten_institutionengenau'] = False;
						}
						$klosterstandorte[$k][$i]['koordinaten'] = $breite . "," . $laenge;
						$bemerkung_standort = $klosterstandort->getBemerkung_standort();
						if (!empty($bemerkung_standort)) {
							$bemerkung_standortArr[] = $bemerkung_standort;
							$klosterstandorte[$k][$i]['bemerkung_standort'] = $bemerkung_standort;
						}
						unset($bemerkung_standort);
						$gruender = $klosterstandort->getGruender();
						if (!empty($gruender)) {
							$gruenderArr[] = $gruender;
							$klosterstandorte[$k][$i]['gruender'] = $gruender;
						} else {
							$klosterstandorte[$k][$i]['gruender'] = '';
						}

						$von_von = $klosterstandort->getVon_von();
						if (!empty($von_von)) {
							$von_vonArr[] = $von_von;
						}

						$von_bis = $klosterstandort->getVon_bis();
						if (!empty($von_bis)) {
							$von_bisArr[] = $von_bis;
						} else {
							if (!empty($von_von)) {
								$von_bisArr[] = $von_von;
								$von_bis = $von_von;
							} else {
								$von_vonArr[] = self::DISTANT_PAST;
								$von_von = self::DISTANT_PAST;
								$von_bisArr[] = self::DISTANT_PAST;
								$von_bis = self::DISTANT_PAST;
							}
						}

						$von_verbal = $klosterstandort->getVon_verbal();
						if (!empty($von_verbal)) {
							$von_verbalArr[] = $von_verbal;
						} else {
							if (!empty($von_von)) {
								if ($von_von != self::DISTANT_PAST && $von_von != self::DISTANT_FUTURE) {
									$von_verbalArr[] = (string)$von_von;
									$von_verbal = (string)$von_von;
								}
								else {
									$von_verbalArr[] = '';
									$von_verbal = '';
								}
							}
							else {
								$von_verbalArr[] = '';
								$von_verbal = '';
							}
						}

						$vonArr[] = intval($von_von);

						$klosterstandorte[$k][$i]['standort_von_von'] = $von_von;
						$klosterstandorte[$k][$i]['standort_von_bis'] = $von_bis;

						if (!empty($von_verbal)) {
							$klosterstandorte[$k][$i]['standort_von_verbal'] = $von_verbal;
						} else {
							$klosterstandorte[$k][$i]['standort_von_verbal'] = '';
						}
						$bis_von = $klosterstandort->getBis_von();
						$bis_bis = $klosterstandort->getBis_bis();
						if (!empty($bis_von)) {
							$bis_vonArr[] = $bis_von;
							if (empty($bis_bis)) {
								$bis_bis = $bis_von;
								$bis_bisArr[] = $bis_von;
							} else {
								$bis_bisArr[] = $bis_bis;
							}
						} else {
							if (!empty($bis_bis)) {
								$bis_vonArr[] = $von_von;
								$bis_von = $von_von;
							} else {
								$bis_vonArr[] = self::DISTANT_PAST;
								$bis_bisArr[] = self::DISTANT_FUTURE;

								$bis_von = self::DISTANT_PAST;
								$bis_bis = self::DISTANT_FUTURE;
							}
						}

						$bis_verbal = $klosterstandort->getBis_verbal();
						if (!empty($bis_verbal)) {
							$bis_verbalArr[] = $bis_verbal;
						} else {
							if (!empty($bis_von)) {
								if ($bis_von != self::DISTANT_PAST && $bis_von != self::DISTANT_FUTURE) {
									if ($von_von != $bis_von) {
										$bis_verbalArr[] = (string)$bis_von;
										$bis_verbal = (string)$bis_von;
									}
								}
								else {
									$bis_verbalArr[] = '';
									$bis_verbal = '';
								}
							}
							else {
								$bis_verbalArr[] = '';
								$bis_verbal = '';
							}
						}

						$bisArr[] = intval($bis_bis);

						$klosterstandorte[$k][$i]['standort_bis_von'] = $bis_von;
						$klosterstandorte[$k][$i]['standort_bis_bis'] = $bis_bis;

						if (!empty($bis_verbal)) {
							$klosterstandorte[$k][$i]['standort_bis_verbal'] = $bis_verbal;
						} else {
							$klosterstandorte[$k][$i]['standort_bis_verbal'] = '';
						}
						$ortObj = $klosterstandort->getOrt();
						if (is_object($ortObj)) {
							$klosterstandorte[$k][$i]['ort'] = $ortObj->getOrt();
							$klosterstandorte[$k][$i]['wuestung'] = $ortObj->getWuestung();

							$ort = $ortObj->getOrt();
							if (!empty($ort)) {
								$ortArr[] = $ort;
								$klosterstandorte[$k][$i]['ort'] = $ort;
							}

							$ortuid = $ortObj->getUid();
							if (!empty($ortuid)) {
								$ortuidArr[] = $ortuid;
								$klosterstandorte[$k][$i]['ort_uid'] = $ortuid;
							}

							$wuestung = $ortObj->getWuestung();
							if ($wuestung) {
								$wuestungArr[] = $wuestung;
								$klosterstandorte[$k][$i]['wuestung'] = $wuestung;
							} else {
								$wuestungArr[] = '';
								$klosterstandorte[$k][$i]['wuestung'] = '';
							}

							$landObj = $ortObj->getLand();
							if (is_object(($landObj))) {
								$land = $landObj->getLand();
								if (!empty($land)) {
									$landArr[] = $land;
									$klosterstandorte[$k][$i]['land'] = $land;
								}
								$ist_in_deutschland = $landObj->getIst_in_deutschland();
								if (!empty($ist_in_deutschland)) {
									$ist_in_deutschlandArr[] = $ist_in_deutschland;
									$klosterstandorte[$k][$i]['ist_in_deutschland'] = $ist_in_deutschland;
								}

								$ortGeonameArr = array();
								$ortUrls = $ortObj->getOrtHasUrls();
								foreach ($ortUrls as $ortUrl) {
									$ortUrlObj = $ortUrl->getUrl();
									$ortUrl = $ortUrlObj->getUrl();
									$ortUrlTypObj = $ortUrlObj->getUrltyp();
									$ortUrlTyp = $ortUrlTypObj->getName();
									if ($ortUrlTyp == "Geonames") {
										$geoname = explode('geonames.org/', $ortUrl)[1];
										$ortGeonameArr[] = $geoname;
										$klosterstandorte[$k][$i]['geonames'] = $geoname;
									}
								}
							}

							$bistumObj = $ortObj->getBistum();
							if (is_object($bistumObj) && $bistumObj->getBistum() !== 'keine Angabe') {

								$bistumuid = $bistumObj->getUid();
								$bistumuidArr[] = $bistumuid;
								$klosterstandorte[$k][$i]['bistum_uid'] = $bistumuid;

								$bistum = $bistumObj->getBistum();

								if (!empty($bistum)) {
									$bistumArr[] = $bistum;
									$klosterstandorte[$k][$i]['bistum'] = $bistum;
								}
								else {
									$bistumArr[] = '';
									$klosterstandorte[$k][$i]['bistum'] = '';
								}

								$kirchenprovinz = $bistumObj->getKirchenprovinz();
								$kirchenprovinzArr[] = $kirchenprovinz;
								$klosterstandorte[$k][$i]['kirchenprovinz'] = $kirchenprovinz;

								$ist_erzbistum = $bistumObj->getIst_erzbistum();
								if ($ist_erzbistum) {
									$ist_erzbistumArr[] = $ist_erzbistum;
									$klosterstandorte[$k][$i]['ist_erzbistum'] = $ist_erzbistum;
								} else {
									$ist_erzbistumArr[] = '';
									$klosterstandorte[$k][$i]['ist_erzbistum'] = '';
								}

								$bistumHasUrls = $bistumObj->getBistumHasUrls();
								foreach ($bistumHasUrls as $bistumHasUrl) {
									$urlObj = $bistumHasUrl->getUrl();
									$bistumUrl = $urlObj->getUrl();
									$urlTypObj = $urlObj->getUrltyp();
									$urlTyp = $urlTypObj->getName();
									if ($urlTyp == "Wikipedia") {
										$klosterArr[$k]['bistum_wikipedia'] = rawurldecode($bistumUrl);
										$klosterstandorte[$k][$i]['bistum_wikipedia'] = rawurldecode($bistumUrl);
									} elseif ($urlTyp == "GND") {
										$components = explode("/gnd/", $bistumUrl);
										if (count($components) > 1) {
											$klosterArr[$k]['bistum_gnd'] = $components[1];
											$klosterstandorte[$k][$i]['bistum_gnd'] = $components[1];
										}
									}
								}
							}
							else {
								$bistumArr[] = '';
								$klosterstandorte[$k][$i]['bistum'] = '';
								$ist_erzbistumArr[] = '';
								$klosterstandorte[$k][$i]['ist_erzbistum'] = '';
							}
						}

						$klosterstandort_von = intval($klosterstandorte[$k][$i]['standort_von_von']);
						$klosterstandort_bis = intval($klosterstandorte[$k][$i]['standort_bis_bis']);
						$standort_jahr50 = array();
						$start = self::MIN_YEAR;
						while ($start < self::MAX_YEAR) {
							if ($klosterstandort_von < ($start + self::YEAR_STEP) && $start <= $klosterstandort_bis) {
								$standort_jahr50[$start] = True;
								$kloster_standort_jahr50[] = $start;
							}
							$start += self::YEAR_STEP;
						}

						if (is_array($standort_jahr50) && !empty($standort_jahr50)) {
							$klosterstandorte[$k][$i]['standort_jahr50'] = array_keys($standort_jahr50);
							$klosterstandorte[$k][$i]['jahr50'] = array_keys($standort_jahr50);
						}
						unset($standort_jahr50);
					}
				}

				$ordenuidArr = array();
				$ordenArr = array();
				$ordenbemerkungArr = array();
				$ordoArr = array();
				$klosterstatusArr = array();
				$ordenstypArr = array();
				$ordengraphikArr = array();
				$ordensymbolArr = array();
				$ko_von_vonArr = array();
				$ko_von_bisArr = array();
				$ko_von_verbalArr = array();
				$ko_bis_vonArr = array();
				$ko_bis_bisArr = array();
				$ko_bis_verbalArr = array();

				$ordenFacetArr = array();

				$kloster_orden_jahr50 = array();
				$start = self::MIN_YEAR;

				$klosterordens = $kloster->getKlosterordens();
				foreach ($klosterordens as $i => $ko) {
					$ordenuid = $ko->getUid();
					$orden = $ko->getOrden();
					$bemerkung = $ko->getBemerkung();
					$ordenUUID = $orden->getUUID();
					$ordenObj = $this->ordenRepository->findByIdentifier($ordenUUID);
					$ordo = $ordenObj->getOrdo();
					$ordenstyp = $ordenObj->getOrdenstyp();
					$graphikdatei = explode('.png', $ordenObj->getGraphik());
					$graphik = $graphikdatei[0];

					$symbol = $ordenObj->getSymbol();
					$klosterstatus = $ko->getKlosterstatus();

					if (!empty($ordenuid)) {
						$ordenuidArr[] = $ordenuid;
						$klosterorden[$k][$i]['id'] = 'kloster-orden-' . $ordenuid;
					}
					if (!empty($orden)) {
						$ordenArr[] = $orden;
						$klosterorden[$k][$i]['orden'] = $orden;
					}
					if (!empty($bemerkung)) {
						$ordenbemerkungArr[] = $bemerkung;
						$klosterorden[$k][$i]['bemerkung_orden'] = $bemerkung;
					}
					if (!empty($ordo)) {
						$ordoArr[] = $ordo;
						$klosterorden[$k][$i]['orden_ordo'] = $ordo;
					}
					if (!empty($klosterstatus)) {
						$klosterstatusArr[] = $klosterstatus;
						$klosterorden[$k][$i]['kloster_status'] = $klosterstatus;
					}
					if (!empty($ordenstyp)) {
						$ordenstypArr[] = $ordenstyp;
						$klosterorden[$k][$i]['orden_typ'] = $ordenstyp;
					}
					if (!empty($graphik)) {
						$ordengraphikArr[] = $graphik;
						$klosterorden[$k][$i]['orden_graphik'] = $graphik;
					}
					if (!empty($symbol)) {
						$ordensymbolArr[] = $symbol;
						$klosterorden[$k][$i]['orden_symbol'] = $symbol;
					}

					$klosterorden[$k][$i]['kloster_id'] = $kloster_id;
					$klosterorden[$k][$i]['typ'] = 'kloster-orden';
					$klosterorden[$k][$i]['sql_uid'] = $ordenuid;

					if (isset($orden) && $orden != 'evangelisches Kloster/Stift' && $orden != 'Reformiertes Stift (calvinistisch)') {
						$klosterorden[$k][$i]['orden_facet'] = $orden;

						$ordenFacetArr[] = $orden;
					}

					$ko_von_von = $ko->getVon_von();
					if (!empty($ko_von_von)) {
						$ko_von_vonArr[] = $ko_von_von;
					}

					$ko_von_bis = $ko->getVon_bis();
					if (!empty($ko_von_bis)) {
						$ko_von_bisArr[] = $ko_von_bis;
					} else {
						if (!empty($ko_von_von)) {
							$ko_von_bisArr[] = $ko_von_von;
							$ko_von_bis = $ko_von_von;
						} else {
							$ko_von_vonArr[] = self::DISTANT_PAST;
							$ko_von_von = self::DISTANT_PAST;
							$ko_von_bisArr[] = self::DISTANT_PAST;
							$ko_von_bis = self::DISTANT_PAST;
						}
					}

					$ko_von_verbal = $ko->getVon_verbal();
					if (!empty($ko_von_verbal)) {
						$ko_von_verbalArr[] = $ko_von_verbal;
					} else {
						if (!empty($ko_von_von)) {
							if ($ko_von_von != self::DISTANT_PAST && $ko_von_von != self::DISTANT_FUTURE) {
								$ko_von_verbalArr[] = (string)$ko_von_von;
								$ko_von_verbal = (string)$ko_von_von;
							}
							else {
								$ko_von_verbalArr[] = '';
								$ko_von_verbal = '';
							}
						}
						else {
							$ko_von_verbalArr[] = '';
							$ko_von_verbal = '';
						}
					}

					$klosterorden[$k][$i]['orden_von_von'] = $ko_von_von;
					$klosterorden[$k][$i]['orden_von_bis'] = $ko_von_bis;

					if (!empty($ko_von_verbal)) {
						$klosterorden[$k][$i]['orden_von_verbal'] = $ko_von_verbal;
					} else {
						$klosterorden[$k][$i]['orden_von_verbal'] = '';
					}

					$ko_bis_von = $ko->getBis_von();
					$ko_bis_bis = $ko->getBis_bis();
					if (!empty($ko_bis_von)) {
						$ko_bis_vonArr[] = $ko_bis_von;
						if (empty($ko_bis_bis)) {
							$ko_bis_bis = $ko_bis_von;
							$ko_bis_bisArr[] = $ko_bis_von;
						} else {
							$ko_bis_bisArr[] = $ko_bis_bis;
						}
					} else {
						if (!empty($ko_bis_bis)) {
							$ko_bis_vonArr[] = $ko_von_von;
							$ko_bis_von = $ko_von_von;
						} else {
							$ko_bis_vonArr[] = self::DISTANT_PAST;
							$ko_bis_bisArr[] = self::DISTANT_FUTURE;

							$ko_bis_von = self::DISTANT_PAST;
							$ko_bis_bis = self::DISTANT_FUTURE;
						}
					}

					$ko_bis_verbal = $ko->getBis_verbal();
					if (!empty($ko_bis_verbal)) {
						$ko_bis_verbalArr[] = $ko_bis_verbal;
					} else {
						if (!empty($ko_bis_von)) {
							if ($ko_bis_von != self::DISTANT_PAST && $ko_bis_von != self::DISTANT_FUTURE) {
								$ko_bis_verbalArr[] = (string)$ko_bis_von;
								$ko_bis_verbal = (string)$ko_bis_von;
							}
							else {
								$ko_bis_verbalArr[] = '';
								$ko_bis_verbal = '';
							}
						}
						else {
							$ko_bis_verbalArr[] = '';
							$ko_bis_verbal = '';
						}
					}

					$klosterorden[$k][$i]['orden_bis_von'] = $ko_bis_von;
					$klosterorden[$k][$i]['orden_bis_bis'] = $ko_bis_bis;

					if (!empty($ko_bis_verbal)) {
						$klosterorden[$k][$i]['orden_bis_verbal'] = $ko_bis_verbal;
					} else {
						$klosterorden[$k][$i]['orden_bis_verbal'] = '';
					}

					$ordengndArr = array();
					$ordenwikipediaArr = array();
					$ordenHasUrls = $ordenObj->getOrdenHasUrls();
					foreach ($ordenHasUrls as $ordenHasUrl) {
						$urlObj = $ordenHasUrl->getUrl();
						$ordenUrl = $urlObj->getUrl();
						$urlTypObj = $urlObj->getUrltyp();
						$urlTyp = $urlTypObj->getName();
						if ($urlTyp == "Wikipedia") {
							$ordenwikipediaArr[] = rawurldecode($ordenUrl);
							$klosterorden[$k][$i]['orden_wikipedia'] = rawurldecode($ordenUrl);
						} elseif ($urlTyp == "GND") {
							$components = explode("/gnd/", $ordenUrl);
							if (count($components) > 1) {
								$ordengndArr[] = $components[1];
								$klosterorden[$k][$i]['orden_gnd'] = $components[1];
							}
						}
					}

					$klosterorden_von = intval($klosterorden[$k][$i]['orden_von_von']);
					$klosterorden_bis = intval($klosterorden[$k][$i]['orden_bis_bis']);
					$orden_jahr50 = array();
					$start = self::MIN_YEAR;
					while ($start < self::MAX_YEAR) {
						if ($klosterorden_von < ($start + self::YEAR_STEP) && $start <= $klosterorden_bis) {
							$orden_jahr50[$start] = True;
							$kloster_orden_jahr50[] = $start;
						}
						$start += self::YEAR_STEP;
					}

					if (is_array($orden_jahr50) && !empty($orden_jahr50)) {
						$klosterorden[$k][$i]['orden_jahr50'] = array_keys($orden_jahr50);
						$klosterorden[$k][$i]['jahr50'] = array_keys($orden_jahr50);
					}

					unset($orden_jahr50);
				}

				if (array_key_exists($sql_uid, $this->personen)) {

					$personenArr = $this->personen[$sql_uid];

					$person_nameArr = array();
					$person_namensalternativenArr = array();
					$person_gsoArr = array();
					$person_gndArr = array();
					$person_bezeichnungArr = array();
					$person_bezeichnung_pluralArr = array();
					$person_anmerkungArr = array();
					$person_von_verbalArr = array();
					$person_vonArr = array();
					$person_bis_verbalArr = array();
					$person_bisArr = array();
					$person_office_idArr = array();

					foreach ($personenArr as $value) {
						$person_nameArr[] = (string)$value['person_name'];
						$person_namensalternativenArr[] = (string)$value['person_namensalternativen'];;
						$person_gsoArr[] = (string)$value['person_gso'];
						$person_gndArr[] = (string)$value['person_gnd'];
						$person_bezeichnungArr[] = (string)$value['person_bezeichnung'];
						$person_bezeichnung_pluralArr[] = (string)$value['person_bezeichnung_plural'];
						$person_anmerkungArr[] = (string)$value['person_anmerkung'];
						$person_von_verbalArr[] = (string)$value['person_von_verbal'];
						$person_vonArr[] = intval($value['person_von']);
						$person_bis_verbalArr[] = (string)$value['person_bis_verbal'];
						$person_bisArr[] = intval($value['person_bis']);
						$person_office_idArr[] = (string)$value['person_office_id'];
					}
				}

				$standortOrdenCount = 1;

				if (isset($klosterorden[$k])) {

					foreach ($klosterorden[$k] as $m => $myorden) {

						if (isset($klosterstandorte[$k])) {

							foreach ($klosterstandorte[$k] as $n => $mystandort) {

								if (($myorden['orden_von_von'] < $mystandort['standort_bis_bis']) && ($mystandort['standort_von_von'] < $myorden['orden_bis_bis'])) {

									$standort_ordenArr[$k][$m][$n]['kloster_id'] = (string)$sql_uid;
									$standort_ordenArr[$k][$m][$n]['id'] = 'standort-orden-' . (string)$sql_uid . '-' . (string)$standortOrdenCount;
									$standort_ordenArr[$k][$m][$n]['sql_uid'] = (string)$sql_uid;
									$standort_ordenArr[$k][$m][$n]['typ'] = 'standort-orden';
									$standort_ordenArr[$k][$m][$n]['patrozinium'] = $patrozinium;
									$standort_ordenArr[$k][$m][$n]['kloster'] = $kloster;
									$standort_ordenArr[$k][$m][$n]['bemerkung_kloster'] = $bemerkung_kloster;
									$standort_ordenArr[$k][$m][$n]['text_gs_band'] = $text_gs_band;
									$standort_ordenArr[$k][$m][$n]['band_seite'] = $band_seite;
									if (isset($band_facet) && !empty($band_facet)) {
										$standort_ordenArr[$k][$m][$n]['band_facet'] = $band_facet;
									}
									$standort_ordenArr[$k][$m][$n]['bearbeitungsstatus'] = $bearbeitungsstatus;
									$standort_ordenArr[$k][$m][$n]['personallistenstatus'] = $personallistenstatus;
									$standort_ordenArr[$k][$m][$n]['koordinaten'] = $mystandort['koordinaten'];
									$standort_ordenArr[$k][$m][$n]['koordinaten_institutionengenau'] = $mystandort['koordinaten_institutionengenau'];
									$standort_ordenArr[$k][$m][$n]['standort_von_von'] = $mystandort['standort_von_von'];
									$standort_ordenArr[$k][$m][$n]['standort_von_bis'] = $mystandort['standort_von_bis'];

									if (!empty($mystandort['standort_von_verbal'])) {
										$standort_ordenArr[$k][$m][$n]['standort_von_verbal'] = $mystandort['standort_von_verbal'];
									} else {
										$standort_ordenArr[$k][$m][$n]['standort_von_verbal'] = '';
									}

									$standort_ordenArr[$k][$m][$n]['standort_bis_von'] = $mystandort['standort_bis_von'];
									$standort_ordenArr[$k][$m][$n]['standort_bis_bis'] = $mystandort['standort_bis_bis'];

									if (!empty($mystandort['standort_bis_verbal'])) {
										$standort_ordenArr[$k][$m][$n]['standort_bis_verbal'] = $mystandort['standort_bis_verbal'];
									} else {
										$standort_ordenArr[$k][$m][$n]['standort_bis_verbal'] = '';
									}

									$standort_ordenArr[$k][$m][$n]['standort_uid'] = explode('-', $mystandort['id'])[2];

									if (!empty($mystandort['gruender'])) {
										$standort_ordenArr[$k][$m][$n]['gruender'] = $mystandort['gruender'];
									} else {
										$standort_ordenArr[$k][$m][$n]['gruender'] = '';
									}

									if (!empty($mystandort['bemerkung_standort'])) {
										$standort_ordenArr[$k][$m][$n]['bemerkung_standort'] = $mystandort['bemerkung_standort'];
									}

									if (!empty($mystandort['ort'])) {
										$standort_ordenArr[$k][$m][$n]['ort'] = $mystandort['ort'];
									}

									if (!empty($mystandort['land'])) {
										$standort_ordenArr[$k][$m][$n]['land'] = $mystandort['land'];
									}
									if (!empty($mystandort['ort_uid'])) {
										$standort_ordenArr[$k][$m][$n]['ort_uid'] = $mystandort['ort_uid'];
									}
									if (!empty($mystandort['ist_in_deutschland'])) {
										$standort_ordenArr[$k][$m][$n]['ist_in_deutschland'] = $mystandort['ist_in_deutschland'];
									}
									if (!empty($mystandort['geonames'])) {
										$standort_ordenArr[$k][$m][$n]['geonames'] = $mystandort['geonames'];
									}
									if (!empty($mystandort['wuestung'])) {
										$standort_ordenArr[$k][$m][$n]['wuestung'] = $mystandort['wuestung'];
									}
									if (!empty($mystandort['bistum'])) {
										$standort_ordenArr[$k][$m][$n]['bistum'] = $mystandort['bistum'];
									}
									if (!empty($mystandort['ist_erzbistum'])) {
										$standort_ordenArr[$k][$m][$n]['ist_erzbistum'] = $mystandort['ist_erzbistum'];
									} else {
										$standort_ordenArr[$k][$m][$n]['ist_erzbistum'] = '';
									}
									if (!empty($mystandort['bistum_gnd'])) {
										$standort_ordenArr[$k][$m][$n]['bistum_gnd'] = $mystandort['bistum_gnd'];
									}
									if (!empty($mystandort['bistum_wikipedia'])) {
										$standort_ordenArr[$k][$m][$n]['bistum_wikipedia'] = $mystandort['bistum_wikipedia'];
									}
									if (!empty($mystandort['bistum_uid'])) {
										$standort_ordenArr[$k][$m][$n]['bistum_uid'] = $mystandort['bistum_uid'];
									}
									if (!empty($mystandort['kirchenprovinz'])) {
										$standort_ordenArr[$k][$m][$n]['kirchenprovinz'] = $mystandort['kirchenprovinz'];
									}
									$standort_ordenArr[$k][$m][$n]['orden'] = $myorden['orden'];
									if (!empty($myorden['orden_ordo'])) {
										$standort_ordenArr[$k][$m][$n]['orden_ordo'] = $myorden['orden_ordo'];
									}
									$standort_ordenArr[$k][$m][$n]['orden_typ'] = $myorden['orden_typ'];
									if (isset($myorden['orden_facet']) && !empty($myorden['orden_facet'])) {
										$standort_ordenArr[$k][$m][$n]['orden_facet'] = $myorden['orden_facet'];
									}
									$standort_ordenArr[$k][$m][$n]['orden_von_von'] = $myorden['orden_von_von'];
									$standort_ordenArr[$k][$m][$n]['orden_von_bis'] = $myorden['orden_von_bis'];

									if (!empty($myorden['orden_von_verbal'])) {
										$standort_ordenArr[$k][$m][$n]['orden_von_verbal'] = $myorden['orden_von_verbal'];
									} else {
										$standort_ordenArr[$k][$m][$n]['orden_von_verbal'] = '';
									}

									$standort_ordenArr[$k][$m][$n]['orden_bis_von'] = $myorden['orden_bis_von'];
									$standort_ordenArr[$k][$m][$n]['orden_bis_bis'] = $myorden['orden_bis_bis'];

									if (!empty($myorden['orden_bis_verbal'])) {
										$standort_ordenArr[$k][$m][$n]['orden_bis_verbal'] = $myorden['orden_bis_verbal'];
									} else {
										$standort_ordenArr[$k][$m][$n]['orden_bis_verbal'] = '';
									}

									$standort_ordenArr[$k][$m][$n]['kloster_orden_uid'] = explode('-', $myorden['id'])[2];

									if (!empty($myorden['orden_gnd'])) {
										$standort_ordenArr[$k][$m][$n]['orden_gnd'] = $myorden['orden_gnd'];
									}
									if (!empty($myorden['orden_wikipedia'])) {
										$standort_ordenArr[$k][$m][$n]['orden_wikipedia'] = $myorden['orden_wikipedia'];
									}
									if (!empty($myorden['orden_graphik'])) {
										$standort_ordenArr[$k][$m][$n]['orden_graphik'] = $myorden['orden_graphik'];
									}
									if (!empty($myorden['orden_symbol'])) {
										$standort_ordenArr[$k][$m][$n]['orden_symbol'] = $myorden['orden_symbol'];
									}

									if (!empty($myorden['kloster_status'])) {
										$standort_ordenArr[$k][$m][$n]['kloster_status'] = $myorden['kloster_status'];
									}
									if (!empty($myorden['bemerkung_orden'])) {
										$standort_ordenArr[$k][$m][$n]['bemerkung_orden'] = $myorden['bemerkung_orden'];
									}

									if (!empty($klosterArr[$k]['literatur_citekey'])) {
										$standort_ordenArr[$k][$m][$n]['literatur_citekey'] = $klosterArr[$k]['literatur_citekey'];
									} else {
										$standort_ordenArr[$k][$m][$n]['literatur_citekey'] = '';
									}

									if (!empty($klosterArr[$k]['literatur_beschreibung'])) {
										$standort_ordenArr[$k][$m][$n]['literatur_beschreibung'] = $klosterArr[$k]['literatur_beschreibung'];
									} else {
										$standort_ordenArr[$k][$m][$n]['literatur_beschreibung'] = '';
									}

									if (!empty($url_wikipedia)) {
										$standort_ordenArr[$k][$m][$n]['url_wikipedia'] = $url_wikipedia;
									}

									if (!empty($url)) {
										$standort_ordenArr[$k][$m][$n]['url'] = $url;
										$standort_ordenArr[$k][$m][$n]['url_typ'] = $urlTyp;
										$standort_ordenArr[$k][$m][$n]['url_bemerkung'] = $url_bemerkung;
										$standort_ordenArr[$k][$m][$n]['url_relation'] = $url_relation;
									}

									if (!empty($url_quelle)) {
										$standort_ordenArr[$k][$m][$n]['url_quelle'] = $url_quelle;
										$standort_ordenArr[$k][$m][$n]['url_quelle_titel'] = $url_quelle_titel;
									}

									if (!empty($gnd)) {
										$standort_ordenArr[$k][$m][$n]['gnd'] = $gnd;
									}

									$standort_ordenArr[$k][$m][$n]['orden_standort_von'] = max($myorden['orden_von_von'], $mystandort['standort_von_von']);
									$standort_ordenArr[$k][$m][$n]['orden_standort_bis'] = min($myorden['orden_bis_bis'], $mystandort['standort_bis_bis']);

									$orden_standort_jahr50 = array();
									$start = self::MIN_YEAR;
									while ($start < self::MAX_YEAR) {
										if ($standort_ordenArr[$k][$m][$n]['orden_standort_von'] < ($start + self::YEAR_STEP) && $start <= $standort_ordenArr[$k][$m][$n]['orden_standort_bis']) {
											$orden_standort_jahr50[$start] = True;
										}
										$start += self::YEAR_STEP;
									}
									if (is_array($orden_standort_jahr50) && !empty($orden_standort_jahr50)) {
										$standort_ordenArr[$k][$m][$n]['orden_standort_jahr50'] = array_keys($orden_standort_jahr50);
									}
									unset($orden_standort_jahr50);

									$orden_jahr50 = array();
									$start = self::MIN_YEAR;
									while ($start < self::MAX_YEAR) {
										if ($myorden['orden_von_von'] < ($start + self::YEAR_STEP) && $start <= $myorden['orden_bis_bis']) {
											$orden_jahr50[$start] = True;
										}
										$start += self::YEAR_STEP;
									}
									$standort_ordenArr[$k][$m][$n]['orden_jahr50'] = array_keys($orden_jahr50);

									$standort_jahr50 = array();
									$start = self::MIN_YEAR;
									while ($start < self::MAX_YEAR) {
										if ($mystandort['standort_von_von'] < ($start + self::YEAR_STEP) && $start <= $mystandort['standort_bis_bis']) {
											$standort_jahr50[$start] = True;
										}
										$start += self::YEAR_STEP;
									}

									if (is_array($standort_jahr50) && !empty($standort_jahr50)) {
										$standort_ordenArr[$k][$m][$n]['standort_jahr50'] = array_keys($standort_jahr50);
									}

									$standort_ordenArr[$k][$m][$n]['jahr50'] = array_merge(array_keys($orden_jahr50), array_keys($standort_jahr50));

									unset($orden_jahr50);

									unset($standort_jahr50);

									$standortOrdenCount++;

									if (isset($band_facet)) unset($band_facet);
								}
							}
						}
					}
				}

				if (isset($person_nameArr) && !empty($person_nameArr)) {
					$klosterArr[$k]['person_name'] = $person_nameArr;
				}

				unset($person_nameArr);

				if (isset($person_namensalternativenArr) && !empty($person_namensalternativenArr)) {
					$klosterArr[$k]['person_namensalternativen'] = $person_namensalternativenArr;
				}

				unset($person_namensalternativenArr);

				if (isset($person_gsoArr) && !empty($person_gsoArr)) {
					$klosterArr[$k]['person_gso'] = $person_gsoArr;
				}

				unset($person_gsoArr);

				if (isset($person_gndArr) && !empty($person_gndArr)) {
					$klosterArr[$k]['person_gnd'] = $person_gndArr;
				}

				unset($person_gndArr);

				if (isset($person_bezeichnungArr) && !empty($person_bezeichnungArr)) {
					$klosterArr[$k]['person_bezeichnung'] = $person_bezeichnungArr;
				}

				unset($person_bezeichnungArr);

				if (isset($person_bezeichnung_pluralArr) && !empty($person_bezeichnung_pluralArr)) {
					$klosterArr[$k]['person_bezeichnung_plural'] = $person_bezeichnung_pluralArr;
				}

				unset($person_bezeichnung_pluralArr);

				if (isset($person_anmerkungArr) && !empty($person_anmerkungArr)) {
					$klosterArr[$k]['person_anmerkung'] = $person_anmerkungArr;
				}

				unset($person_anmerkungArr);

				if (isset($person_von_verbalArr) && !empty($person_von_verbalArr)) {
					$klosterArr[$k]['person_von_verbal'] = $person_von_verbalArr;
				}

				unset($person_von_verbalArr);

				if (isset($person_vonArr) && !empty($person_vonArr)) {
					$klosterArr[$k]['person_von'] = $person_vonArr;
				}

				unset($person_vonArr);

				if (isset($person_bis_verbalArr) && !empty($person_bis_verbalArr)) {
					$klosterArr[$k]['person_bis_verbal'] = $person_bis_verbalArr;
				}

				unset($person_bis_verbalArr);

				if (isset($person_bisArr) && !empty($person_bisArr)) {
					$klosterArr[$k]['person_bis'] = $person_bisArr;
				}

				unset($person_bisArr);

				if (isset($person_office_idArr) && !empty($person_office_idArr)) {
					$klosterArr[$k]['person_office_id'] = $person_office_idArr;
				}

				unset($person_office_idArr);

				if (isset($standortuidArr) && !empty($standortuidArr)) {
					$klosterArr[$k]['standort_uid'] = $standortuidArr;
				}

				if (isset($koordinatenArr) && !empty($koordinatenArr)) {
					$klosterArr[$k]['koordinaten'] = $koordinatenArr;
				}

				if (isset($koordinaten_institutionengenauArr) && !empty($koordinaten_institutionengenauArr)) {
					$klosterArr[$k]['koordinaten_institutionengenau'] = $koordinaten_institutionengenauArr;
				}

				if (isset($gruenderArr) && !empty($gruenderArr)) {
					$klosterArr[$k]['gruender'] = $gruenderArr;
				} else {
					$klosterArr[$k]['gruender'] = '';
				}

				if (isset($von_vonArr) && !empty($von_vonArr)) {
					$klosterArr[$k]['standort_von_von'] = ($von_vonArr);
				}

				if (isset($von_bisArr) && !empty($von_bisArr)) {
					$klosterArr[$k]['standort_von_bis'] = ($von_bisArr);
				}

				if (isset($von_verbalArr) && !empty($von_verbalArr)) {
					$klosterArr[$k]['standort_von_verbal'] = $von_verbalArr;
				} else {
					$klosterArr[$k]['standort_von_verbal'] = '';
				}

				if (isset($vonArr) && !empty($vonArr)) {
					$klosterArr[$k]['von'] = min($vonArr);
				}

				if (isset($bis_vonArr) && !empty($bis_vonArr)) {
					$klosterArr[$k]['standort_bis_von'] = ($bis_vonArr);
				}

				if (isset($bis_bisArr) && !empty($bis_bisArr)) {
					$klosterArr[$k]['standort_bis_bis'] = ($bis_bisArr);
				}

				if (isset($bis_verbalArr) && !empty($bis_verbalArr)) {
					$klosterArr[$k]['standort_bis_verbal'] = $bis_verbalArr;
				} else {
					$klosterArr[$k]['standort_bis_verbal'] = '';
				}

				if (isset($bemerkung_standortArr) && !empty($bemerkung_standortArr)) {
					$klosterArr[$k]['bemerkung_standort'] = $bemerkung_standortArr;
				} else {
					$klosterArr[$k]['bemerkung_standort'] = '';
				}

				if (isset($bisArr) && !empty($bisArr)) {
					$klosterArr[$k]['bis'] = min($bisArr);
				}

				if (isset($ortArr) && !empty($ortArr)) {
					$klosterArr[$k]['ort'] = $ortArr;
					if (count($ortArr > 0)) {
						$klosterArr[$k]['ort_sort'] = $ortArr[0];
					}
				}

				if (isset($ortuidArr) && !empty($ortuidArr)) {
					$klosterArr[$k]['ort_uid'] = $ortuidArr;
				}

				if (isset($wuestungArr) && !empty($wuestungArr)) {
					$klosterArr[$k]['wuestung'] = $wuestungArr;
				} else {
					$klosterArr[$k]['wuestung'] = '';
				}

				if (isset($landArr) && !empty($landArr)) {
					$klosterArr[$k]['land'] = $landArr;
				}

				if (isset($ist_in_deutschlandArr) && !empty($ist_in_deutschlandArr)) {
					$klosterArr[$k]['ist_in_deutschland'] = $ist_in_deutschlandArr;
				}

				if (isset($ortGeonameArr) && !empty($ortGeonameArr)) {
					$klosterArr[$k]['geonames'] = $ortGeonameArr;
				}

				if (isset($bistumuidArr) && !empty($bistumuidArr)) {
					$klosterArr[$k]['bistum_uid'] = $bistumuidArr;
				}

				if (isset($bistumArr) && !empty($bistumArr)) {
					$klosterArr[$k]['bistum'] = $bistumArr;
				}
				else {
					$klosterArr[$k]['bistum'] = '';
				}

				if (isset($kirchenprovinzArr) && !empty($kirchenprovinzArr)) {
					$klosterArr[$k]['kirchenprovinz'] = $kirchenprovinzArr;
				}

				if (isset($ist_erzbistumArr) && !empty($ist_erzbistumArr)) {
					$klosterArr[$k]['ist_erzbistum'] = $ist_erzbistumArr;
				} else {
					$klosterArr[$k]['ist_erzbistum'] = '';
				}

				if (isset($ordenuidArr) && !empty($ordenuidArr)) {
					$klosterArr[$k]['kloster_orden_uid'] = $ordenuidArr;
				}

				if (isset($ordenArr) && !empty($ordenArr)) {
					$klosterArr[$k]['orden'] = $ordenArr;
				}

				if (isset($ordenbemerkungArr) && !empty($ordenbemerkungArr)) {
					$klosterArr[$k]['bemerkung_orden'] = $ordenbemerkungArr;
				}

				if (isset($ordoArr) && !empty($ordoArr)) {
					$klosterArr[$k]['orden_ordo'] = $ordoArr;
				}

				if (isset($klosterstatusArr) && !empty($klosterstatusArr)) {
					$klosterArr[$k]['kloster_status'] = $klosterstatusArr;
				}

				if (isset($ordenstypArr) && !empty($ordenstypArr)) {
					$klosterArr[$k]['orden_typ'] = $ordenstypArr;
				}

				if (isset($ordenstypArr) && !empty($ordenstypArr)) {
					$klosterArr[$k]['orden_facet'] = $ordenFacetArr;
				}

				if (isset($ko_von_vonArr) && !empty($ko_von_vonArr)) {
					$klosterArr[$k]['orden_von_von'] = $ko_von_vonArr;
				}

				if (isset($ko_von_bisArr) && !empty($ko_von_bisArr)) {
					$klosterArr[$k]['orden_von_bis'] = $ko_von_bisArr;
				}

				if (isset($ko_von_verbalArr) && !empty($ko_von_verbalArr)) {
					$klosterArr[$k]['orden_von_verbal'] = $ko_von_verbalArr;
				} else {
					$klosterArr[$k]['orden_von_verbal'] = '';
				}

				if (isset($ko_bis_vonArr) && !empty($ko_bis_vonArr)) {
					$klosterArr[$k]['orden_bis_von'] = $ko_bis_vonArr;
				}

				if (isset($ko_bis_bisArr) && !empty($ko_bis_bisArr)) {
					$klosterArr[$k]['orden_bis_bis'] = $ko_bis_bisArr;
				}

				if (isset($ko_bis_verbalArr) && !empty($ko_bis_verbalArr)) {
					$klosterArr[$k]['orden_bis_verbal'] = $ko_bis_verbalArr;
				} else {
					$klosterArr[$k]['orden_bis_verbal'] = '';
				}

				if (isset($ordengraphikArr) && !empty($ordengraphikArr)) {
					$klosterArr[$k]['orden_graphik'] = $ordengraphikArr;
				}
				if (isset($ordensymbolArr) && !empty($ordensymbolArr)) {
					$klosterArr[$k]['orden_symbol'] = $ordensymbolArr;
				}

				if (isset($ordengndArr) && !empty($ordengndArr)) {
					$klosterArr[$k]['orden_gnd'] = $ordengndArr;
				}
				if (isset($ordenwikipediaArr) && !empty($ordenwikipediaArr)) {
					$klosterArr[$k]['orden_wikipedia'] = $ordenwikipediaArr;
				}

				$klosterArr[$k]['standort_jahr50'] = $kloster_standort_jahr50;

				$klosterArr[$k]['orden_jahr50'] = $kloster_orden_jahr50;

				$kloster_jahr50 = array_merge($kloster_standort_jahr50, $kloster_orden_jahr50);
				$klosterArr[$k]['jahr50'] = $kloster_jahr50;
			}

			return array($klosterArr, $klosterstandorte, $klosterorden, $standort_ordenArr);

		} else {
			$this->logger->log('Database seems to be empty.');
			exit;
		}
	}

}

?>