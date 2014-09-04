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
	 * @ORM\Column(nullable=TRUE)
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 * @ORM\JoinColumn(onDelete="No ACTION")
	 */
	protected $kloster;

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
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Orden
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $orden;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Klosterstatus
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $klosterstatus;
	
	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $bemerkung;

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