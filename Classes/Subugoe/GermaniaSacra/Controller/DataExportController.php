<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerFactory;
use TYPO3\Flow\Mvc\Controller\ActionController;

ini_set('memory_limit', '-1');

class DataExportController extends ActionController
{
    /**
     * @var \TYPO3\Flow\Security\Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeiterRepository
     */
    protected $bearbeiterRepository;

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
    protected $configuration = [];

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
     * @var string
     */
    const executeExportDump = 'executeDataExport.txt';

    /**
     * @var string
     */
    const exportLogFile = 'dataExport.log';

    /**
     * @var string
     */
    protected $dataImport;

    /**
     * @param array $settings
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    public function __construct($logger = null)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function initializeAction()
    {
        $this->configuration = [
                'endpoint' => [
                        'localhost' => [
                                'host' => $this->settings['solr']['host'],
                                'port' => $this->settings['solr']['port'],
                                'path' => $this->settings['solr']['path'],
                                'core' => $this->settings['solr']['core'],
                                'timeout' => $this->settings['solr']['timeout']
                        ]
                ],
        ];
        if (!$this->logger) {
            $log = new LoggerFactory();

            $this->logger = $log->create(
                    'GermaniaSacra',
                    'TYPO3\Flow\Log\Logger',
                    '\TYPO3\Flow\Log\Backend\FileBackend',
                    [
                            'logFileUrl' => FLOW_PATH_DATA . 'Logs/GermaniaSacra/Mysql2Solr.log',
                            'createParentDirectories' => true
                    ]
            );
        }
        $this->client = new \Solarium\Client($this->configuration);
        $this->client->setAdapter('Solarium\Core\Client\Adapter\Curl');
        $personenFile = FLOW_PATH_DATA . 'Persistent/GermaniaSacra/personen.json';
        $http = new \Guzzle\Http\Client();
        try {
            $personenData = $http->get(self::PERSONEN_URL)->send()->getBody();
            file_put_contents($personenFile, $personenData);
        } catch (\Exception $e) {
            $this->logger->logException($e);
        }
        $this->personen = json_decode(file_get_contents($personenFile), true);
        $this->dataImport = new \Subugoe\GermaniaSacra\Controller\DataImportController();
    }

    /**
     */
    public function deleteAction()
    {
        // get an update query instance
        $update = $this->client->createUpdate();
        // add the delete query and a commit command to the update query
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        // this executes the query and returns the result
        $this->client->execute($update);
    }

    /**
     * Exports Kloster data from mysql into solr
     *
     * @return bool
     */
    public function mysql2solrExportAction()
    {
        $this->dataImport->initializeLogger(self::exportLogFile);
        $jobOwnerFileContent = file_get_contents($this->dataImport->dumpDirectory . self::executeExportDump);
        $this->dataImport->importExportLogger->log($jobOwnerFileContent);
        $start = date('d.m.Y H:i:s');
        $date1 = new \DateTime($start);
        $this->dataImport->importExportLogger->log('Start am ' . $start);
        if (file_exists($this->dataImport->dumpDirectory . self::executeExportDump)) {
            unlink($this->dataImport->dumpDirectory . self::executeExportDump);
        }
        $klosterData = $this->klosterListAllAction();
        $klosterArr = $klosterData[0];
        $klosterstandortArr = $klosterData[1];
        $klosterordenArr = $klosterData[2];
        $standort_ordenArr = $klosterData[3];
        $update = $this->client->createUpdate();
        $docs = [];
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
        $this->deleteAction();
        /** @var \Solarium\Core\Query\Result\ResultInterface $result */
        $result = $this->client->execute($update);
        $logMessage = 'Data export completed in ' . round($result->getQueryTime() / 100) . ' seconds.';
        $this->logger->log($logMessage);
        $end = date('d.m.Y H:i:s');
        $date2 = new \DateTime($end);
        $this->dataImport->importExportLogger->log('Ende am ' . $end);
        $this->dataImport->importExportLogger->log('Dauer ' . $date1->diff($date2)->i . " Minuten und " . $date1->diff($date2)->s . ' Sekunden');
        return $logMessage;
    }

    /**
     * Displays the content of data export log file
     */
    public function exportLogAction()
    {
        $exportLogFile = $this->dataImport->logDirectory . self::exportLogFile;
        if (file_exists($exportLogFile)) {
            echo nl2br(file_get_contents($exportLogFile));
        }
        exit;
    }

    /**
     * @return array $reponse The list of all Kloster in json format
     */
    public function klosterListAllAction()
    {
        if ($this->personen === null) {
            $this->logger->log('Personendatenbank ist nicht verfügbar.');
            exit;
        }
        $this->klosterRepository->setDefaultOrderings(
                ['uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING]
        );
        $klosters = $this->klosterRepository->findAll();
        if (!empty($klosters)) {
            $klosterArr = [];
            $klosterstandorte = [];
            $klosterorden = [];
            $standort_ordenArr = [];
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
                /** @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatusObj */
                $bearbeitungsstatusObj = $kloster->getBearbeitungsstatus();
                $bearbeitungsstatus = $bearbeitungsstatusObj->getName();
                $klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus;
                /** @var \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $personallistenstatusObj */
                $personallistenstatusObj = $kloster->getPersonallistenstatus();
                $personallistenstatus = $personallistenstatusObj->getName();
                $klosterArr[$k]['personallistenstatus'] = $personallistenstatus;
                $klosterArr[$k]['typ'] = 'kloster';
                $klosterArr[$k]['id'] = (string)$kloster->getKloster_id();
                /** @var \Subugoe\GermaniaSacra\Domain\Model\Band $band */
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
                        /** @var \Subugoe\GermaniaSacra\Domain\Model\Url $urlObj */
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
                        /** @var \Subugoe\GermaniaSacra\Domain\Model\Url $urlObj */
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
                $citekey = [];
                $beschreibung = [];
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
                $standortuidArr = [];
                $koordinatenArr = [];
                $koordinaten_institutionengenauArr = [];
                $von_vonArr = [];
                $von_bisArr = [];
                $von_verbalArr = [];
                $vonArr = [];
                $bis_vonArr = [];
                $bis_bisArr = [];
                $bis_verbalArr = [];
                $bisArr = [];
                $ortArr = [];
                $ortuidArr = [];
                $wuestungArr = [];
                $landArr = [];
                $ist_in_deutschlandArr = [];
                $bistumuidArr = [];
                $bistumArr = [];
                $kirchenprovinzArr = [];
                $ist_erzbistumArr = [];
                $bemerkung_standortArr = [];
                $kloster_standort_jahr50 = [];
                $start = self::MIN_YEAR;
                $klosterstandorts = $kloster->getKlosterstandorts();
                foreach ($klosterstandorts as $i => $klosterstandort) {
                    /** @var /** @var \Subugoe\GermaniaSacra\Domain\Model\Ort $ortObj */
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
                            $koordinaten_institutionengenauArr[] = true;
                            $klosterstandorte[$k][$i]['koordinaten_institutionengenau'] = true;
                        } else {
                            $ortObj = $klosterstandort->getOrt();
                            $breite = $ortObj->getBreite();
                            $laenge = $ortObj->getLaenge();
                            $koordinatenArr[] = $breite . "," . $laenge;
                            $koordinaten_institutionengenauArr[] = false;
                            $klosterstandorte[$k][$i]['koordinaten_institutionengenau'] = false;
                        }
                        $klosterstandorte[$k][$i]['koordinaten'] = $breite . "," . $laenge;
                        $bemerkung_standort = $klosterstandort->getBemerkung_standort();
                        if (!empty($bemerkung_standort)) {
                            $bemerkung_standortArr[] = $bemerkung_standort;
                            $klosterstandorte[$k][$i]['bemerkung_standort'] = $bemerkung_standort;
                        } else {
                            $bemerkung_standortArr[] = '';
                            $klosterstandorte[$k][$i]['bemerkung_standort'] = '';
                        }
                        unset($bemerkung_standort);
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
                                } else {
                                    $von_verbalArr[] = '';
                                    $von_verbal = '';
                                }
                            } else {
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
                                } else {
                                    $bis_verbalArr[] = '';
                                    $bis_verbal = '';
                                }
                            } else {
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
                                $ortGeonameArr = [];
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
                                } else {
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
                            } else {
                                $bistumArr[] = '';
                                $klosterstandorte[$k][$i]['bistum'] = '';
                                $ist_erzbistumArr[] = '';
                                $klosterstandorte[$k][$i]['ist_erzbistum'] = '';
                            }
                        }
                        $klosterstandort_von = intval($klosterstandorte[$k][$i]['standort_von_von']);
                        $klosterstandort_bis = intval($klosterstandorte[$k][$i]['standort_bis_bis']);
                        $standort_jahr50 = [];
                        $start = self::MIN_YEAR;
                        while ($start < self::MAX_YEAR) {
                            if ($klosterstandort_von < ($start + self::YEAR_STEP) && $start <= $klosterstandort_bis) {
                                $standort_jahr50[$start] = true;
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
                $ordenuidArr = [];
                $ordenArr = [];
                $ordenbemerkungArr = [];
                $ordoArr = [];
                $klosterstatusArr = [];
                $ordenstypArr = [];
                $ordengraphikArr = [];
                $ordensymbolArr = [];
                $ko_von_vonArr = [];
                $ko_von_bisArr = [];
                $ko_von_verbalArr = [];
                $ko_bis_vonArr = [];
                $ko_bis_bisArr = [];
                $ko_bis_verbalArr = [];
                $ordenFacetArr = [];
                $kloster_orden_jahr50 = [];
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
                            } else {
                                $ko_von_verbalArr[] = '';
                                $ko_von_verbal = '';
                            }
                        } else {
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
                            } else {
                                $ko_bis_verbalArr[] = '';
                                $ko_bis_verbal = '';
                            }
                        } else {
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
                    $ordengndArr = [];
                    $ordenwikipediaArr = [];
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
                    $orden_jahr50 = [];
                    $start = self::MIN_YEAR;
                    while ($start < self::MAX_YEAR) {
                        if ($klosterorden_von < ($start + self::YEAR_STEP) && $start <= $klosterorden_bis) {
                            $orden_jahr50[$start] = true;
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
                    $person_nameArr = [];
                    $person_namensalternativenArr = [];
                    $person_gsoArr = [];
                    $person_gndArr = [];
                    $person_bezeichnungArr = [];
                    $person_bezeichnung_pluralArr = [];
                    $person_anmerkungArr = [];
                    $person_von_verbalArr = [];
                    $person_vonArr = [];
                    $person_bis_verbalArr = [];
                    $person_bisArr = [];
                    $person_office_idArr = [];
                    foreach ($personenArr as $value) {
                        $person_nameArr[] = (string)$value['person_name'];
                        $person_namensalternativenArr[] = (string)$value['person_namensalternativen'];
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
                                if (($myorden['orden_von_von'] <= $mystandort['standort_bis_bis']) && ($mystandort['standort_von_von'] <= $myorden['orden_bis_bis'])) {
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
                                    if (!empty($mystandort['bemerkung_standort'])) {
                                        $standort_ordenArr[$k][$m][$n]['bemerkung_standort'] = $mystandort['bemerkung_standort'];
                                    } else {
                                        $standort_ordenArr[$k][$m][$n]['bemerkung_standort'] = '';
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
                                    $orden_standort_jahr50 = [];
                                    $start = self::MIN_YEAR;
                                    while ($start < self::MAX_YEAR) {
                                        if ($standort_ordenArr[$k][$m][$n]['orden_standort_von'] < ($start + self::YEAR_STEP) && $start <= $standort_ordenArr[$k][$m][$n]['orden_standort_bis']) {
                                            $orden_standort_jahr50[$start] = true;
                                        }
                                        $start += self::YEAR_STEP;
                                    }
                                    if (is_array($orden_standort_jahr50) && !empty($orden_standort_jahr50)) {
                                        $standort_ordenArr[$k][$m][$n]['orden_standort_jahr50'] = array_keys($orden_standort_jahr50);
                                    }
                                    unset($orden_standort_jahr50);
                                    $orden_jahr50 = [];
                                    $start = self::MIN_YEAR;
                                    while ($start < self::MAX_YEAR) {
                                        if ($myorden['orden_von_von'] < ($start + self::YEAR_STEP) && $start <= $myorden['orden_bis_bis']) {
                                            $orden_jahr50[$start] = true;
                                        }
                                        $start += self::YEAR_STEP;
                                    }
                                    $standort_ordenArr[$k][$m][$n]['orden_jahr50'] = array_keys($orden_jahr50);
                                    $standort_jahr50 = [];
                                    $start = self::MIN_YEAR;
                                    while ($start < self::MAX_YEAR) {
                                        if ($mystandort['standort_von_von'] < ($start + self::YEAR_STEP) && $start <= $mystandort['standort_bis_bis']) {
                                            $standort_jahr50[$start] = true;
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
                                    if (isset($band_facet)) {
                                        unset($band_facet);
                                    }
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
                } else {
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
            return [$klosterArr, $klosterstandorte, $klosterorden, $standort_ordenArr];
        } else {
            $this->logger->log('Database seems to be empty.');
            exit;
        }
    }

    /**
     * Creats a file to be checked be cronjob before exporting the data
     */
    public function dataexportAction()
    {
        $dumpDirectory = $this->dataImport->dumpDirectory;
        $executeDumpExportFile = $dumpDirectory . self::executeExportDump;
        if (!file_exists($executeDumpExportFile) && $fileHandle = fopen($executeDumpExportFile, "w")) {
            $txt = '';
            if ($this->securityContext->canBeInitialized()) {
                if ($account = $this->securityContext->getAccount()) {
                    $jobOwner = $this->bearbeiterRepository->findOneByAccount($account);
                    $txt = 'Dieser Export wurde angelegt von ' . $jobOwner;
                }
            }
            fwrite($fileHandle, $txt);
            fclose($fileHandle);
            $currentTimeMinutes = date('i');
            $minutesFraction = substr($currentTimeMinutes, 1, 1);
            $nextImportDumpExecution = 10 - $minutesFraction;
            echo 'Die nächste Veröffentlichung wird in ' . $nextImportDumpExecution . ' Minuten durchgeführt.' . '<br>';
            echo 'Sie dauert ca. 5 Minuten.' . '<br>';
        }
        elseif (file_exists($executeDumpExportFile)) {
            echo "Die Veröffentlichung ist bereits vorgemerkt.";
        }
        else {
            echo "Der Veröffentlichung-Job konnte leider nicht angelegt werden.";
        }
        exit;
    }
}