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
class Band {

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster>
	 * @ORM\OneToMany(mappedBy="band")
	 */
	protected $klosters;

	/**
	 * @var string
	 */
	protected $nummer;

	/**
	 * @var integer
	 */
	protected $sortierung;

	/**
	 * @var string
	 */
	protected $titel;

	/**
	 * @var string
	 */
	protected $kurztitel;

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
	 * @return string
	 */
	public function getNummer() {
		return $this->nummer;
	}

	/**
	 * @param string $nummer
	 * @return void
	 */
	public function setNummer($nummer) {
		$this->nummer = $nummer;
	}
	
	/**
	 * @return integer
	 */
	public function getSortierung() {
		return $this->sortierung;
	}

	/**
	 * @param string $sortierung
	 * @return void
	 */
	public function setSortierung($sortierung) {
		$this->sortierung = $sortierung;
	}
	
	/**
	 * @return string
	 */
	public function getTitel() {
		return $this->titel;
	}

	/**
	 * @param string $titel
	 * @return void
	 */
	public function setTitel($titel) {
		$this->titel = $titel;
	}

	/**
	 * @return string
	 */
	public function getKurztitel() {
		return $this->kurztitel;
	}

	/**
	 * @param string $kurztitel
	 * @return void
	 */
	public function setKurztitel($kurztitel) {
		$this->kurztitel = $kurztitel;
	}	
	
	public function __toString()
	{
	  return $this->getTitel();
	}
}
?>