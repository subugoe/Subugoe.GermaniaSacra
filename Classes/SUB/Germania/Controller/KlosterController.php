<?php
namespace SUB\Germania\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use SUB\Germania\Domain\Model\Kloster;

class KlosterController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\KlosterRepository
	 */
	protected $klosterRepository;

	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\OrtRepository
	 */
	protected $ortRepository;

	/**
	 * @Flow\Inject
	 * @var \SUB\Germania\Domain\Repository\KlosterstandortRepository
	 */
	protected $klosterstandortRepository;

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
			$klosterArr[$k]['bearbeitungsstatus'] = $bearbeitungsstatus->getName();
						
			$klosterstandorts = $kloster->getKlosterstandorts();
						
			foreach ($klosterstandorts as $i => $klosterstandort) {
				$ort = $klosterstandort->getOrt();
				$klosterArr[$k]['ort'][$i] = $ort->getUUID();
			}
		}

		$ortGemeindeKreis = array();
		$orte = $this->ortRepository->findAll();
		foreach ($orte as $l=>$ort) {
			$klosterArr[$k]['ortGemeindeKreis'][$l] = array($ort->getOrtGemeindeKreis() => $ort->getUUID());
		}

		return json_encode($klosterArr);
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('klosters', $this->klosterRepository->findAll());
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function showAction(Kloster $kloster) {
		$this->view->assign('kloster', $kloster);
	}

	/**
	 * @return void
	 */
	public function newAction() {
		$this->view->assign('orts', $this->ortRepository->findAll());
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Kloster $newKloster
	 * @return void
	 */
	public function createAction(Kloster $newKloster) {
	
		$this->klosterRepository->add($newKloster);
				            
		return json_encode(array(1));

		// $this->addFlashMessage('Created a new kloster.');
		// $this->redirect('index');
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function editAction(Kloster $kloster) {
		$klosterArr = array();
		$klosterArr['uuid'] = $kloster->getUUID();
		$klosterArr['uid'] = $kloster->getUid();
		$klosterArr['kloster'] = $kloster->getKloster();
		$klosterArr['kloster_id'] = $kloster->getKloster_id();
		$klosterArr['patrozinium'] = $kloster->getPatrozinium();
		$klosterArr['bemerkung'] = $kloster->getBemerkung();
		$klosterArr['band_seite'] = $kloster->getBand_seite();
		$klosterArr['text_gs_band'] = $kloster->getText_gs_band();
		
		$band = $kloster->getBand();
		$klosterArr['band'] = $band->getTitel();

		$bearbeitungsstatus = $kloster->getBearbeitungsstatus();
		$klosterArr['bearbeitungsstatus'] = $bearbeitungsstatus->getName();

		$personallistenstatus = $kloster->getPersonallistenstatus();
		$klosterArr['personallistenstatus'] = $personallistenstatus->getName();

		
		$klosterstandorte = array();
		$klosterstandorts = $kloster->getKlosterstandorts();
		foreach ($klosterstandorts as $i => $klosterstandort) {
		
			$klosterstandorte[$i]['breite'] = $klosterstandort->getBreite();
			$klosterstandorte[$i]['laenge'] = $klosterstandort->getLaenge();
			$klosterstandorte[$i]['gruender'] = $klosterstandort->getGruender();
			$klosterstandorte[$i]['bemerkung_standort'] = $klosterstandort->getBemerkung_standort();
			
			$ort = $klosterstandort->getOrt();
			$klosterstandorte[$i]['ort'] = $ort->getOrt();
			$klosterstandorte[$i]['wuestung'] = $ort->getWuestung();
			
			$zeitraum = $klosterstandort->getZeitraum();
			$klosterstandorte[$i]['von_von'] = $zeitraum->getVon_von();
			$klosterstandorte[$i]['von_bis'] = $zeitraum->getVon_bis();
			$klosterstandorte[$i]['von_verbal'] = $zeitraum->getVon_verbal();
			$klosterstandorte[$i]['bis_von'] = $zeitraum->getBis_von();
			$klosterstandorte[$i]['bis_bis'] = $zeitraum->getBis_bis();
			$klosterstandorte[$i]['bis_verbal'] = $zeitraum->getBis_verbal();
		}
		$klosterArr['klosterstandorte'] = $klosterstandorte;


		$klosterorden = array();
		$klosterordens = $kloster->getKlosterordens();
		foreach ($klosterordens as $i => $ko) {
			$klosterorden[$i]['bemerkung_orden'] = $ko->getBemerkung();
			
			$orden = $ko->getOrden();
			$klosterorden[$i]['orden'] = $orden->getOrden();
			
			$klosterstatus = $ko->getKlosterstatus();
			$klosterorden[$i]['klosterstatus'] = $klosterstatus->getStatus();
			
			$zeitraum = $ko->getZeitraum();
			$klosterorden[$i]['orden_von_von'] = $zeitraum->getVon_von();
			$klosterorden[$i]['orden_von_bis'] = $zeitraum->getVon_bis();
			$klosterorden[$i]['orden_von_verbal'] = $zeitraum->getVon_verbal();
			$klosterorden[$i]['orden_bis_von'] = $zeitraum->getBis_von();
			$klosterorden[$i]['orden_bis_bis'] = $zeitraum->getBis_bis();
			$klosterorden[$i]['orden_bis_verbal'] = $zeitraum->getBis_verbal();
		}
		$klosterArr['klosterorden'] = $klosterorden;

		return json_encode($klosterArr);
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function updateAction(Kloster $kloster) {
	
		
		// $klosterstandorts = $this->klosterstandortRepository->findByKloster($kloster);
		// foreach ($klosterstandorts as $klosterstandort) {
		
			// echo $klosterstandort->getUid();
			
			// $this->klosterstandortRepository->remove($klosterstandort);
		// }
		$this->klosterRepository->update($kloster);
		
				
		$klosterstandorts = $this->klosterstandortRepository->findByKloster($kloster);
		foreach ($klosterstandorts as $klosterstandort) {
		
			// echo $klosterstandort->getUid();
			
			$this->klosterstandortRepository->remove($klosterstandort);
		}
		
		return json_encode(array(1));
		
		// $this->addFlashMessage('Updated the kloster.');
		// $this->redirect('index');
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Kloster $kloster
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