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
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\ManyToOne(inversedBy="klosterstandorts")
	 */
	protected $kloster;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
	 * @ORM\ManyToOne(inversedBy="klosterstandorts")
	 */
	protected $ort;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Zeitraum
	 * @ORM\OneToOne(mappedBy="klosterstandort")
	 */
	protected $zeitraum;
	
	/**
	 * @var string
	 */
	protected $gruender;

	/**
	 * @var string
	 */
	protected $bemerkung;

	/**
	 * @var float
	 */
	protected $breite;

	/**
	 * @var float
	 */
	protected $laenge;

	/**
	 * @var string
	 */
	protected $bemerkung_standort;

	/**
	 * @var string
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
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Zeitraum
	 */
	public function getZeitraum() {
		return $this->zeitraum;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Zeitraum $zeitraum
	 * @return void
	 */
	public function setZeitraum(\Subugoe\GermaniaSacra\Domain\Model\Zeitraum $zeitraum) {
		$this->zeitraum = $zeitraum;
	}

	/**
	 * @return integer
	 */
	public function getUid() {
		return $this->uid;
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

	public function __toString()
	{
	  return $this->getGruender();
	}
}
?>