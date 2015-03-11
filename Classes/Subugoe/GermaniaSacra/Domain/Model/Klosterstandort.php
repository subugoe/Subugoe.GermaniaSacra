<?php
namespace Subugoe\GermaniaSacra\Domain\Model;


use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Klosterstandort {

	/**
	 * @var integer
	 * @ORM\Column(nullable=TRUE)
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\ManyToOne(inversedBy="klosterstandorts")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $kloster;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
	 * @ORM\ManyToOne
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $ort;

	/**
	 * @var integer
	 * @ORM\Column(nullable=true)
	 */
	protected $von_von;

	/**
	 * @var integer
	 * @ORM\Column(nullable=true)
	 */
	protected $von_bis;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $von_verbal;

	/**
	 * @var integer
	 * @ORM\Column(nullable=true)
	 */
	protected $bis_von;

	/**
	 * @var integer
	 * @ORM\Column(nullable=true)
	 */
	protected $bis_bis;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $bis_verbal;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $gruender;

	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $bemerkung;

	/**
	 * @var float
	 * @ORM\Column(nullable=true)
	 */
	protected $breite;

	/**
	 * @var float
	 * @ORM\Column(nullable=true)
	 */
	protected $laenge;

	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $bemerkung_standort;

	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $temp_literatur_alt;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 */
	public function getKloster() {
		return $this->kloster;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function setKloster(\Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster) {
		$this->kloster = $kloster;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Ort
	 */
	public function getOrt() {
		return $this->ort;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function setOrt(\Subugoe\GermaniaSacra\Domain\Model\Ort $ort) {
		$this->ort = $ort;
	}

	/**
	 * @return integer
	 */
	public function getVon_von() {
		return $this->von_von;
	}

	/**
	 * @param string $von_von
	 * @return void
	 */
	public function setVon_von($von_von) {
		$this->von_von = $von_von;
	}

	/**
	 * @return integer
	 */
	public function getVon_bis() {
		return $this->von_bis;
	}

	/**
	 * @param string $von_bis
	 * @return void
	 */
	public function setVon_bis($von_bis) {
		$this->von_bis = $von_bis;
	}

	/**
	 * @return string
	 */
	public function getVon_verbal() {
		return $this->von_verbal;
	}

	/**
	 * @param string $von_verbal
	 * @return void
	 */
	public function setVon_verbal($von_verbal) {
		$this->von_verbal = $von_verbal;
	}

	/**
	 * @return integer
	 */
	public function getBis_von() {
		return $this->bis_von;
	}

	/**
	 * @param string $bis_von
	 * @return void
	 */
	public function setBis_von($bis_von) {
		$this->bis_von = $bis_von;
	}

	/**
	 * @return integer
	 */
	public function getBis_bis() {
		return $this->bis_bis;
	}

	/**
	 * @param string $bis_bis
	 * @return void
	 */
	public function setBis_bis($bis_bis) {
		$this->bis_bis = $bis_bis;
	}

	/**
	 * @return string
	 */
	public function getBis_verbal() {
		return $this->bis_verbal;
	}

	/**
	 * @param string $bis_verbal
	 * @return void
	 */
	public function setBis_verbal($bis_verbal) {
		$this->bis_verbal = $bis_verbal;
	}

	/**
	 * @return integer
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * @param integer $uid
	 * @return void
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * @return string
	 */
	public function getGruender() {
		return $this->gruender;
	}

	/**
	 * @param string $gruender
	 * @return void
	 */
	public function setGruender($gruender) {
		$this->gruender = $gruender;
	}

	/**
	 * @return string
	 */
	public function getBemerkung() {
		return $this->bemerkung;
	}

	/**
	 * @param string $bemerkung
	 * @return void
	 */
	public function setBemerkung($bemerkung) {
		$this->bemerkung = $bemerkung;
	}

	/**
	 * @return float
	 */
	public function getBreite() {
		return $this->breite;
	}

	/**
	 * @param float $breite
	 * @return void
	 */
	public function setBreite($breite) {
		$this->breite = $breite;
	}

	/**
	 * @return float
	 */
	public function getLaenge() {
		return $this->laenge;
	}

	/**
	 * @param float $laenge
	 * @return void
	 */
	public function setLaenge($laenge) {
		$this->laenge = $laenge;
	}

	/**
	 * @return string
	 */
	public function getBemerkung_standort() {
		return $this->bemerkung_standort;
	}

	/**
	 * @param string $bemerkung_standort
	 * @return void
	 */
	public function setBemerkung_standort($bemerkung_standort) {
		$this->bemerkung_standort = $bemerkung_standort;
	}

	/**
	 * @return string
	 */
	public function getTemp_literatur_alt() {
		return $this->temp_literatur_alt;
	}

	/**
	 * @param string $temp_literatur_alt
	 * @return void
	 */
	public function setTemp_literatur_alt($temp_literatur_alt) {
		$this->temp_literatur_alt = $temp_literatur_alt;
	}

	public function __toString() {
		return $this->getGruender();
	}
}

?>