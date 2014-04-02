<?php
namespace SUB\Germania\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

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
	 * @var \SUB\Germania\Domain\Model\Kloster'
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 */
	protected $kloster;

	/**
	 * @var \SUB\Germania\Domain\Model\Zeitraum'
	 * @ORM\OneToOne(mappedBy="klosterorden")
	 */
	protected $zeitraum;

	/**
	 * @var \SUB\Germania\Domain\Model\Orden'
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 */
	protected $orden;

	/**
	 * @var \SUB\Germania\Domain\Model\Klosterstatus'
	 * @ORM\ManyToOne(inversedBy="klosterordens")
	 */
	protected $klosterstatus;
	
	/**
	 * @var string
	 */
	protected $bemerkung;

	/**
	 * @return \SUB\Germania\Domain\Model\Kloster
	 */
	public function getKloster() {
		return $this->kloster;
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function setKloster(\SUB\Germania\Domain\Model\Kloster $kloster) {
		$this->kloster = $kloster;
	}

	/**
	 * @return \SUB\Germania\Domain\Model\Zeitraum
	 */
	public function getZeitraum() {
		return $this->zeitraum;
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Zeitraum $zeitraum
	 * @return void
	 */
	public function setZeitraum(\SUB\Germania\Domain\Model\Zeitraum $zeitraum) {
		$this->zeitraum = $zeitraum;
	}

	/**
	 * @return \SUB\Germania\Domain\Model\Orden
	 */
	public function getOrden() {
		return $this->orden;
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Orden $orden
	 * @return void
	 */
	public function setOrden(\SUB\Germania\Domain\Model\Orden $orden) {
		$this->orden = $orden;
	}

	/**
	 * @return \SUB\Germania\Domain\Model\Klosterstatus
	 */
	public function getKlosterstatus() {
		return $this->klosterstatus;
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Klosterstatus $klosterstatus
	 * @return void
	 */
	public function setKlosterstatus(\SUB\Germania\Domain\Model\Klosterstatus $klosterstatus) {
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