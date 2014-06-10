<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;


class DataExportController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

	/**
	* @var \Solarium\Client
	*/
	protected $client;

	/**
	* Solr configuration Array
	* @var array
	*/
	protected $configuration = array();

	/**
	* @var \TYPO3\Flow\Log\Logger
	*/
	protected $logger;

	public function __construct() {
		$this->configuration = array(
		    'endpoint' => array(
		        'localhost' => array(
		            'host' => '127.0.0.1',
		            'port' => 8983,
		            'path' => '/solr/germaniasacra',
		        )
		    )
		);

		$this->client = new \Solarium\Client($this->configuration);
	}

	public function testAction() {

//		 create a ping query
		$ping = $this->client->createPing();

//		 execute the ping query
		try {
		    $result = $this->client->ping($ping);
		    echo 'Ping query successful';
		    echo '<br/><pre>';
		    var_dump($result->getData());
		}
		catch (\Solarium\Exception\HttpException $exception) {
			echo 'Ping query failed';
		}

		exit;
	}

	public function test1Action() {

		// get a select query instance
		$query = $this->client->createSelect();

		// this executes the query and returns the result
		$resultset = $this->client->execute($query);

		// display the total number of documents found by solr
		echo 'NumFound: '.$resultset->getNumFound();

		// show documents using the resultset iterator
		foreach ($resultset as $document) {

		    echo '<hr/><table>';

		    // the documents are also iterable, to get all fields
		    foreach ($document as $field => $value) {
//		         this converts multivalue fields to a comma-separated string
		        if (is_array($value)) {
		            $value = implode(', ', $value);
		        }

		        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
		    }

		    echo '</table>';
		}
	}

	/**
	 * Exports Kloster data from mysql into solr
	 */

	public function mysql2solrExport() {

//		$kloster = new \Subugoe\GermaniaSacra\Controller\KlosterController();

		$this->klosterListAllAction();
	}

	/**
	 * @return array $reponse The list of all Kloster in json format
	 */
	public function klosterListAllAction() {

		$this->klosterRepository->setDefaultOrderings(
			array( 'uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		$klosters = $this->klosterRepository->findAll();

		$klosterArr = array();
		foreach ($klosters as $k => $kloster) {
			$klosterArr[$k]['uuid'] = $kloster->getUUID();
			$klosterArr[$k]['kloster_id'] = $kloster->getKloster_id();
			$klosterArr[$k]['kloster'] = $kloster->getKloster();
			$klosterArr[$k]['kloster'] = $kloster->getKloster();
			$klosterArr[$k]['patrozinium'] = $kloster->getPatrozinium();
			$klosterArr[$k]['bemerkung_kloster'] = $kloster->getBemerkung();
			$klosterArr[$k]['text_gs_band'] = $kloster->getText_gs_band();
			$klosterArr[$k]['band_seite'] = $kloster->getBand_seite();

			$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
			$klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus->getName();

			$bearbeiter = $kloster->getBearbeiter();
			$klosterArr[$k]['bearbeiter'] = $bearbeiter->getBearbeiter();

			$personallistenstatus = $kloster->getPersonallistenstatus();
			$klosterArr[$k]['personallistenstatus'] = $personallistenstatus->getName();

			$klosterArr[$k]['typ'] = 'kloster';
			$klosterArr[$k]['id'] = (string)$kloster->getKloster_id();

			$band = $kloster->getBand();
			if (is_object($band)) {
				$klosterArr[$k]['band_uuid'] = $band->getUUID();
				$klosterArr[$k]['band_id'] = $band->getUid();
				$klosterArr[$k]['band_nummer'] = $band->getNummer();
				$klosterArr[$k]['band_titel'] = $band->getTitel();
				$klosterArr[$k]['band_kurztitel'] = $band->getKurztitel();
				$klosterArr[$k]['band_sortierung'] = $band->getSortierung();
				$bandSortName = str_pad($band->getSortierung(), 4, "0", STR_PAD_LEFT) . '####' . $band->getNummer() . ' ' . $band->getKurztitel();
				$klosterArr[$k]['band_facet'] = $bandSortName . ", hat_band";

				$bandHasUrls = $band->getBandHasUrls();
				foreach ($bandHasUrls as $bandHasUrl) {
					$urlObj = $bandHasUrl->getUrl();
					$url = $urlObj->getUrl();
					$urlTypObj = $urlObj->getUrltyp();
					$urlTyp = trim($urlTypObj->getName());

//					echo $k . " => " . $kloster->getKloster_id() . " => " . $url . " => " . $urlTyp . "<br>";

					if ($urlTyp == "Handle") {
						$klosterArr[$k]['band_url'] = $url;
					}

					if ($urlTyp == "Findpage") {
						$klosterArr[$k]['band_url_seitengenau'] = $url;
					}
				}
			}

			if (!isset($klosterArr[$k]['band_url'])) {
				$klosterArr[$k]['band_url'] = '';
			}

			if (!isset($klosterArr[$k]['band_url_seitengenau'])) {
				$klosterArr[$k]['band_url_seitengenau'] = '';
			}

			$klosterHasUrls = $kloster->getKlosterHasUrls();
			foreach ($klosterHasUrls as $i => $klosterHasUrl) {
				$urlObj = $klosterHasUrl->getUrl();
				$url = $urlObj->getUrl();
				$urlTypObj = $urlObj->getUrltyp();
				$urlTyp = $urlTypObj->getName();
				if ($urlTyp == "Wikipedia") {
					$klosterArr[$k]['url_wikipedia'] = $url;
				}
				elseif ($urlTyp == "Quelle") {
					$klosterArr[$k]['url_quelle'] = $url;
					$klosterArr[$k]['url_quelle_titel'] = $urlObj->getBemerkung();
				}
				else {
					$klosterArr[$k]['url'] = $url;
					$klosterArr[$k]['"url_bemerkung'] = $urlObj->getBemerkung();
					$klosterArr[$k]['url_typ'] = $urlTyp;
					$klosterArr[$k]['url_relation'] = 'kloster';
				}

				if ($urlTyp == "GND") {
					$components = explode("/gnd/", $url);
					if (count($components) > 1) {
						$klosterArr[$k]['gnd'] = $components[1];
					}
					else {
						echo 'Keine GND URL: ' + $url;
					}
				}
			}

			var_dump($klosterArr[$k]);
			echo "<br><br>";

		}

		die;
//		$response = $klosterArr;
//		return json_encode($response);
	}
}

?>
