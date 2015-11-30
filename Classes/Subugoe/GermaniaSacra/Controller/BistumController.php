<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Bistum;
use Subugoe\GermaniaSacra\Domain\Model\Url;
use Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl;

class BistumController extends AbstractBaseController
{
    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumRepository
     */
    protected $bistumRepository;

    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
     */
    protected $ortRepository;

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
     * @var \Subugoe\GermaniaSacra\Domain\Repository\BistumHasUrlRepository
     */
    protected $bistumHasUrlRepository;

    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\BandRepository
     */
    protected $bandRepository;

    /**
     * @var array
     */
    protected $supportedMediaTypes = ['text/html', 'application/json'];

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = [
            'json' => 'TYPO3\\Flow\\Mvc\\View\\JsonView',
            'html' => 'TYPO3\\Fluid\\View\\TemplateView'
    ];

    /**
     * @var string
     */
    const  start = 0;

    /**
     * @var string
     */
    const  length = 100;

    /**
     * @var string
     */
    protected $defaultViewObjectName = 'TYPO3\\Flow\\Mvc\\View\\JsonView';

    /**
     * Returns the list of all Bistum entities
     */
    public function listAction()
    {
        if ($this->request->getFormat() === 'json') {
            $this->view->setVariablesToRender(['bistum']);
        }
        $searchArr = [];
        if ($this->request->hasArgument('columns')) {
            $columns = $this->request->getArgument('columns');
            foreach ($columns as $column) {
                if (!empty($column['data']) && !empty($column['search']['value'])) {
                    $searchArr[$column['data']] = $column['search']['value'];
                }
            }
        }
        if ($this->request->hasArgument('order')) {
            $order = $this->request->getArgument('order');
            if (!empty($order)) {
                $orderDir = $order[0]['dir'];
                $orderById = $order[0]['column'];
                if (!empty($orderById)) {
                    $columns = $this->request->getArgument('columns');
                    $orderBy = $columns[$orderById]['data'];
                }
            }
        }
        if ($this->request->hasArgument('draw')) {
            $draw = $this->request->getArgument('draw');
        } else {
            $draw = 0;
        }
        $start = $this->request->hasArgument('start') ? $this->request->getArgument('start'):self::start;
        $length = $this->request->hasArgument('length') ? $this->request->getArgument('length'):self::length;
        if (empty($searchArr)) {
            if ((isset($orderBy) && !empty($orderBy)) && (isset($orderDir) && !empty($orderDir))) {
                if ($orderDir === 'asc') {
                    $orderArr = [$orderBy => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING];
                } elseif ($orderDir === 'desc') {
                    $orderArr = [$orderBy => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING];
                }
            }
            if (isset($orderArr) && !empty($orderArr)) {
                $orderings = $orderArr;
            } else {
                $orderings = ['bistum' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING];
            }
            $bistums = $this->bistumRepository->getCertainNumberOfBistum($start, $length, $orderings);
            $recordsTotal = $this->bistumRepository->getNumberOfEntries();
        } else {
            if ((isset($orderBy) && !empty($orderBy)) && (isset($orderDir) && !empty($orderDir))) {
                if ($orderDir === 'asc') {
                    $orderArr = [$orderBy, 'ASC'];
                } elseif ($orderDir === 'desc') {
                    $orderArr = [$orderBy, 'DESC'];
                }
            }
            if (isset($orderArr) && !empty($orderArr)) {
                $orderings = $orderArr;
            } else {
                $orderings = ['bistum', 'ASC'];
            }
            $bistums = $this->bistumRepository->searchCertainNumberOfBistum($start, $length, $orderings, $searchArr, 1);
            $recordsFiltered = $this->bistumRepository->searchCertainNumberOfBistum($start, $length, $orderings, $searchArr, 2);
            $recordsTotal = $this->bistumRepository->getNumberOfEntries();
        }
        if (!isset($recordsFiltered)) {
            $recordsFiltered = $recordsTotal;
        }
        $bistumArr = [];
        foreach ($bistums as $k => $bistum) {
            if (is_object($bistum)) {
                $uUID = $bistum->getUUID();
                if (!empty($uUID)) {
                    $bistumArr[$k]['uUID'] = $uUID;
                } else {
                    $bistumArr[$k]['uUID'] = '';
                }
                $bistumName = $bistum->getBistum();
                if (!empty($bistumName)) {
                    $bistumArr[$k]['bistum'] = $bistumName;
                } else {
                    $bistumArr[$k]['bistum'] = '';
                }
                $kirchenprovinz = $bistum->getKirchenprovinz();
                if (!empty($kirchenprovinz)) {
                    $bistumArr[$k]['kirchenprovinz'] = $kirchenprovinz;
                } else {
                    $bistumArr[$k]['kirchenprovinz'] = '';
                }
                $ist_erzbistum = $bistum->getIst_erzbistum();
                if (!empty($ist_erzbistum)) {
                    $bistumArr[$k]['ist_erzbistum'] = $ist_erzbistum;
                } else {
                    $bistumArr[$k]['ist_erzbistum'] = '';
                }
                $shapefile = $bistum->getShapefile();
                if (!empty($shapefile)) {
                    $bistumArr[$k]['shapefile'] = $shapefile;
                } else {
                    $bistumArr[$k]['shapefile'] = '';
                }
                $bemerkung = $bistum->getBemerkung();
                if (!empty($bemerkung)) {
                    $bistumArr[$k]['bemerkung'] = $bemerkung;
                } else {
                    $bistumArr[$k]['bemerkung'] = '';
                }
                $ortObj = $bistum->getOrt();
                if (is_object($ortObj)) {
                    $ort = $ortObj->getUUID() . ':' . $ortObj->getOrt();
                    if (!empty($ort)) {
                        $bistumArr[$k]['ort'] = $ort;
                    } else {
                        $bistumArr[$k]['ort'] = '';
                    }
                } else {
                    $bistumArr[$k]['ort'] = '';
                }
            }
        }
        $this->view->assign('bistum', ['data' => $bistumArr, 'draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered]);
        $this->view->assign('bearbeiter', $this->bearbeiterObj->getBearbeiter());
        return $this->view->render();
    }

    /**
     * Create a new Bistum entity
     */
    public function createAction()
    {
        $bistumObj = new Bistum();
        if (is_object($bistumObj)) {
            $bistumObj->setBistum($this->request->getArgument('bistum'));
            $bistumObj->setKirchenprovinz($this->request->getArgument('kirchenprovinz'));
            $bistumObj->setBemerkung($this->request->getArgument('bemerkung'));
            $bistumObj->setIst_erzbistum($this->request->hasArgument('ist_erzbistum'));
            $bistumObj->setShapefile($this->request->getArgument('shapefile'));

            if ($this->request->hasArgument('ort')) {
                $ortUUID = $this->request->getArgument('ort');
                $ortObj = $this->ortRepository->findByIdentifier($ortUUID);
                if (is_object($ortObj)) {
                    $bistumObj->setOrt($ortObj);
                }
            }
            $this->bistumRepository->add($bistumObj);
            // Add GND if set
            if ($this->request->hasArgument('gnd')) {
                $gnd = $this->request->getArgument('gnd');
                if ($this->request->hasArgument('gnd_label')) {
                    $gnd_label = $this->request->getArgument('gnd_label');
                }
                if (empty($gnd_label)) {
                    $gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
                    $gnd_label = $this->request->getArgument('bistum') . ' [' . $gndid . ']';
                }
                if (isset($gnd) && !empty($gnd)) {
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
                    $bistumhasurl = new BistumHasUrl();
                    $bistumhasurl->setBistum($bistumObj);
                    $bistumhasurl->setUrl($urlObj);
                    $this->bistumHasUrlRepository->add($bistumhasurl);
                }
            }
            //Update Wikipedia if set
            $bistumHasWiki = false;
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
                    $bistumhasurl = new BistumHasUrl();
                    $bistumhasurl->setBistum($bistumObj);
                    $bistumhasurl->setUrl($urlObj);
                    $this->bistumHasUrlRepository->add($bistumhasurl);
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
                        foreach ($urlArr as $k => $url) {
                            if (!empty($url)) {
                                $urlObj = new Url();
                                $urlObj->setUrl($url);
                                $urlTypObj = $this->urltypRepository->findByIdentifier($urlTypArr[$k]);
                                $urlTyp = $urlTypObj->getName();
                                $urlObj->setUrltyp($urlTypObj);
                                if (isset($linksLabelArr[$k]) && !empty($linksLabelArr[$k])) {
                                    $urlObj->setBemerkung($linksLabelArr[$k]);
                                } else {
                                    $urlObj->setBemerkung($urlTyp);
                                }
                                $this->urlRepository->add($urlObj);
                                $bistumhasurlObj = new BistumHasUrl();
                                $bistumhasurlObj->setBistum($bistumObj);
                                $bistumhasurlObj->setUrl($urlObj);
                                $this->bistumHasUrlRepository->add($bistumhasurlObj);
                            }
                        }
                    }
                }
            }
            $this->persistenceManager->persistAll();
            $this->throwStatus(201, null, null);
        } else {
            $this->throwStatus(400, 'Entity Bistum not available', null);
        }
    }

    /**
     * Edit a Bistum entity
     * @return array $bistumArr
     */
    public function editAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $bistumArr = [];
        $bistumObj = $this->bistumRepository->findByIdentifier($uuid);
        $bistumArr['uUID'] = $bistumObj->getUUID();
        $bistumArr['bistum'] = $bistumObj->getBistum();
        $bistumArr['kirchenprovinz'] = $bistumObj->getKirchenprovinz();
        $bistumArr['bemerkung'] = $bistumObj->getBemerkung();
        $bistumArr['ist_erzbistum'] = $bistumObj->getIst_erzbistum();
        $bistumArr['shapefile'] = $bistumObj->getShapefile();
        $ort = $bistumObj->getOrt();
        if ($ort) {
            $bistumArr['ort'] = ['uUID' => $ort->getUUID(), 'name' => $ort->getOrt()];
        } else {
            $bistumArr['ort'] = [];
        }
        // Bistum Url data
        $Urls = [];
        $bistumHasUrls = $bistumObj->getBistumHasUrls();
        foreach ($bistumHasUrls as $k => $bistumHasUrl) {
            $urlObj = $bistumHasUrl->getUrl();
            $url = rawurldecode($urlObj->getUrl());
            $url_bemerkung = $urlObj->getBemerkung();
            if ($url !== 'keine Angabe') {
                $urlTypObj = $urlObj->getUrltyp();
                if (is_object($urlTypObj)) {
                    $urlTyp = $urlTypObj->getUUID();
                    $urlTypName = $urlTypObj->getName();
                    if ($urlTypName == 'GND' || $urlTypName == 'Wikipedia') {
                        $Urls[$k] = ['url_typ' => $urlTyp, 'url' => $url, 'url_label' => $url_bemerkung, 'url_typ_name' => $urlTypName];
                    } else {
                        $Urls[$k] = ['url_typ' => $urlTyp, 'url' => $url, 'links_label' => $url_bemerkung, 'url_typ_name' => $urlTypName];
                    }
                }
            }
        }
        $bistumArr['url'] = $Urls;
        return json_encode($bistumArr);
    }

    /**
     * Update a Bistum entity
     */
    public function updateAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $bistumObj = $this->bistumRepository->findByIdentifier($uuid);
        if (is_object($bistumObj)) {
            $bistumObj->setBistum($this->request->getArgument('bistum'));
            $bistumObj->setKirchenprovinz($this->request->getArgument('kirchenprovinz'));
            $bistumObj->setBemerkung($this->request->getArgument('bemerkung'));
            $bistumObj->setIst_erzbistum($this->request->hasArgument('ist_erzbistum'));
            $bistumObj->setShapefile($this->request->getArgument('shapefile'));
            if ($this->request->hasArgument('ort')) {
                $ortUUID = $this->request->getArgument('ort');
                $ortObj = $this->ortRepository->findByIdentifier($ortUUID);
                if (is_object($ortObj)) {
                    $bistumObj->setOrt($ortObj);
                }
            }
            $this->bistumRepository->update($bistumObj);
            // Fetch Bistum Urls
            $bistumHasUrls = $bistumObj->getBistumHasUrls();
            $bistumHasGND = false;
            // Update GND if set
            if ($this->request->hasArgument('gnd')) {
                $gnd = $this->request->getArgument('gnd');
                if ($this->request->hasArgument('gnd_label')) {
                    $gnd_label = $this->request->getArgument('gnd_label');
                }
                if (empty($gnd_label)) {
                    $gndid = str_replace('http://d-nb.info/gnd/', '', trim($gnd));
                    $gnd_label = $this->request->getArgument('bistum') . ' [' . $gndid . ']';
                }
                if (isset($gnd) && !empty($gnd)) {
                    if (!empty($bistumHasUrls)) {
                        foreach ($bistumHasUrls as $i => $bistumHasUrl) {
                            $urlObj = $bistumHasUrl->getUrl();
                            $urlTypObj = $urlObj->getUrltyp();
                            $urlTyp = $urlTypObj->getName();
                            if ($urlTyp == "GND") {
                                $urlObj->setUrl($gnd);
                                if (!empty($gnd_label)) {
                                    $urlObj->setBemerkung($gnd_label);
                                }
                                $this->urlRepository->update($urlObj);
                                $bistumHasGND = true;
                            }
                        }
                    }
                    if (!$bistumHasGND) {
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
                        $bistumhasurl = new BistumHasUrl();
                        $bistumhasurl->setBistum($bistumObj);
                        $bistumhasurl->setUrl($urlObj);
                        $this->bistumHasUrlRepository->add($bistumhasurl);
                    }
                }
            }
            //Update Wikipedia if set
            $bistumHasWiki = false;
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
                    foreach ($bistumHasUrls as $i => $bistumHasUrl) {
                        $urlObj = $bistumHasUrl->getUrl();
                        $urlTypObj = $urlObj->getUrltyp();
                        $urlTyp = $urlTypObj->getName();
                        if ($urlTyp == "Wikipedia") {
                            $urlObj->setUrl($wikipedia);
                            if (!empty($wikipedia_label)) {
                                $urlObj->setBemerkung($wikipedia_label);
                            }
                            $this->urlRepository->update($urlObj);
                            $bistumHasWiki = true;
                        }
                    }
                    if (!$bistumHasWiki) {
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
                        $bistumhasurl = new BistumHasUrl();
                        $bistumhasurl->setBistum($bistumObj);
                        $bistumhasurl->setUrl($urlObj);
                        $this->bistumHasUrlRepository->add($bistumhasurl);
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
                        foreach ($bistumHasUrls as $i => $bistumHasUrl) {
                            $urlObj = $bistumHasUrl->getUrl();
                            $urlTypObj = $urlObj->getUrltyp();
                            $urlTyp = $urlTypObj->getName();
                            if ($urlTyp != "Wikipedia" && $urlTyp != "GND") {
                                $this->bistumHasUrlRepository->remove($bistumHasUrl);
                                $this->urlRepository->remove($urlObj);
                            }
                        }
                        foreach ($urlArr as $k => $url) {
                            if (!empty($url)) {
                                $urlObj = new Url();
                                $urlObj->setUrl($url);
                                $urlTypObj = $this->urltypRepository->findByIdentifier($urlTypArr[$k]);
                                $urlTyp = $urlTypObj->getName();
                                $urlObj->setUrltyp($urlTypObj);
                                if (isset($linksLabelArr[$k]) && !empty($linksLabelArr[$k])) {
                                    $urlObj->setBemerkung($linksLabelArr[$k]);
                                } else {
                                    $urlObj->setBemerkung($urlTyp);
                                }
                                $this->urlRepository->add($urlObj);
                                $bistumhasurlObj = new BistumHasUrl();
                                $bistumhasurlObj->setBistum($bistumObj);
                                $bistumhasurlObj->setUrl($urlObj);
                                $this->bistumHasUrlRepository->add($bistumhasurlObj);
                            }
                        }
                    }
                }
            }
            $this->persistenceManager->persistAll();
            $this->throwStatus(200, null, null);
        } else {
            $this->throwStatus(400, 'Entity Bistum not available', null);
        }
    }

    /**
     * Delete an Bistum entity
     */
    public function deleteAction()
    {
        if ($this->request->hasArgument('uUID')) {
            $uuid = $this->request->getArgument('uUID');
        }
        if (empty($uuid)) {
            $this->throwStatus(400, 'Required uUID not provided', null);
        }
        $orte = count($this->ortRepository->findByBistum($uuid));
        $bistumhasurls = count($this->bistumHasUrlRepository->findByBistum($uuid));
        $bands = count($this->bandRepository->findByBistum($uuid));
        if ($orte == 0 && $bistumhasurls == 0 && $bands == 0) {
            $bistumObj = $this->bistumRepository->findByIdentifier($uuid);
            if (!is_object($bistumObj)) {
                $this->throwStatus(400, 'Entity Bistum not available', null);
            }
            $this->bistumRepository->remove($bistumObj);
            // Fetch Bistum Urls
            $bistumHasUrls = $bistumObj->getBistumHasUrls();
            if (is_array($bistumHasUrls)) {
                foreach ($bistumHasUrls as $bistumHasUrl) {
                    $this->bistumHasUrlRepository->remove($bistumHasUrl);
                }
            }
            $this->throwStatus(200, null, null);
        } else {
            $this->throwStatus(400, 'Due to dependencies Bistum entity could not be deleted', null);
        }
    }

    /**
     * Update a list of Bistum entities
     */
    public function updateListAction()
    {
        if ($this->request->hasArgument('data')) {
            $bistumlist = $this->request->getArgument('data');
        }
        if (empty($bistumlist)) {
            $this->throwStatus(400, 'Required data arguemnts not provided', null);
        }
        foreach ($bistumlist as $uuid => $bistum) {
            if (isset($uuid) && !empty($uuid)) {
                $bistumObj = $this->bistumRepository->findByIdentifier($uuid);
                $bistumObj->setBistum($bistum['bistum']);
                $bistumObj->setKirchenprovinz($bistum['kirchenprovinz']);
                $bistumObj->setBemerkung($bistum['bemerkung']);
                if (isset($bistum['ist_erzbistum']) && !empty($bistum['ist_erzbistum'])) {
                    $ist_erzbistum = $bistum['ist_erzbistum'];
                } else {
                    $ist_erzbistum = 0;
                }
                $bistumObj->setIst_erzbistum($ist_erzbistum);
                $bistumObj->setShapefile($bistum['shapefile']);
                $ortUUID = $bistum['ort'];
                $ort = $this->ortRepository->findByIdentifier($ortUUID);
                $bistumObj->setOrt($ort);
                $this->bistumRepository->update($bistumObj);
            } else {
                $this->throwStatus(400, 'Required uUID not provided', null);
            }
        }
        $this->persistenceManager->persistAll();
        $this->throwStatus(200, null, null);
    }
}
