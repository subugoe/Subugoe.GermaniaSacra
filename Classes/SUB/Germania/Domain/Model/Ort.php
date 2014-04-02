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
class Ort {
	/**
	* @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	* @Flow\Inject
	*/
	protected $persistenceManager;

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var string
	 */
	protected $ort;

	/**
	 * @var string
	 */
	protected $gemeinde;

	/**
	 * @var string
	 */
	protected $kreis;

	/**
	 * @var integer
	 */
	protected $wuestung;

	/**
	 * @var float
	 */
	protected $breite;

	/**
	 * @var float
	 */
	protected $laenge;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterstandort>
	 * @ORM\OneToMany(mappedBy="ort", cascade={"all"})
	 */
	protected $klosterstandorts;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Land'
	 * @ORM\ManyToOne(inversedBy="orts")
	 */
	protected $land;

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
	public function getOrt() {
		return $this->ort;
	}

	/**
	 * @param string $ort
	 * @return void
	 */
	public function setOrt($ort) {
		$this->ort = $ort;
	}

	/**
	 * @return string
	 */
	public function getGemeinde() {
		return $this->gemeinde;
	}

	/**
	 * @param string $gemeinde
	 * @return void
	 */
	public function setGemeinde($gemeinde) {
		$this->gemeinde = $gemeinde;
	}

	/**
	 * @return string
	 */
	public function getKreis() {
		return $this->kreis;
	}

	/**
	 * @param string $kreis
	 * @return void
	 */
	public function setKreis($kreis) {
		$this->kreis = $kreis;
	}

	/**
	 * @return integer
	 */
	public function getWuestung() {
		return $this->wuestung;
	}

	/**
	 * @param string $wuestung
	 * @return void
	 */
	public function setWuestung($wuestung) {
		$this->wuestung = $wuestung;
	}

	/**
	 * @return float
	 */
	public function getBreite() {
		return $this->breite;
	}

	/**
	 * @param string $breite
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
	 * @param string $laenge
	 * @return void
	 */
	public function setLaenge($laenge) {
		$this->laenge = $laenge;
	}
	
	public function __toString()
	{
	  return $this->getOrt();
	}

	public function getOrtGemeindeKreis(){
		$ortGemeindeKreis = "";
		if (isset($this->ort) && !empty($this->ort)) {
			$ortGemeindeKreis .= $this->ort;
		}
		if (isset($this->gemeinde) && !empty($this->gemeinde)) {
			$ortGemeindeKreis .= " :: " . $this->gemeinde;
		}
		if (isset($this->kreis) && !empty($this->kreis)) {
			$ortGemeindeKreis .= " :: " . $this->kreis;
		}

		return $ortGemeindeKreis;
	}

	public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

}
?>