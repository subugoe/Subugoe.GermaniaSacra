<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Klosterorden {

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster'
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 */
	protected $kloster;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Zeitraum'
	 * @ORM\OneToOne(mappedBy="klosterorden")
	 */
	protected $zeitraum;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Orden'
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 */
	protected $orden;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Klosterstatus'
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 */
	protected $klosterstatus;
	
	/**
	 * @var string
	 */
	protected $bemerkung;

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
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Orden
	 */
	public function getOrden() {
		return $this->orden;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Orden $orden
	 * @return void
	 */
	public function setOrden(\Subugoe\GermaniaSacra\Domain\Model\Orden $orden) {
		$this->orden = $orden;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Klosterstatus
	 */
	public function getKlosterstatus() {
		return $this->klosterstatus;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Klosterstatus $klosterstatus
	 * @return void
	 */
	public function setKlosterstatus(\Subugoe\GermaniaSacra\Domain\Model\Klosterstatus $klosterstatus) {
		$this->klosterstatus = $klosterstatus;
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
}
?>