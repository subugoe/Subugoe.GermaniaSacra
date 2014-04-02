<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Zeitraum {

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var integer
	 */
	protected $von_von;

	/**
	 * @var integer
	 */
	protected $von_bis;

	/**
	 * @var string
	 */
	protected $von_verbal;

	/**
	 * @var integer
	 */
	protected $bis_von;

	/**
	 * @var integer
	 */
	protected $bis_bis;

	/**
	 * @var string
	 */
	protected $bis_verbal;

	/**
	 * @ORM\OneToOne(inversedBy="zeitraum", cascade={"all"})
	 */
	protected $klosterstandort;

	/**
	 * @ORM\OneToOne(inversedBy="zeitraum", cascade={"all"})
	 */
	protected $klosterorden;

	/**
	 * @return integer
	 */
	public function getuid() {
		return $this->uid;
	}

	/**
	 * @param integer $uid
	 * @return void
	 */
	public function setuid($uid) {
		$this->uid = $uid;
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
}
?>