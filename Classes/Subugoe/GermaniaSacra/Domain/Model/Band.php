<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Band {

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
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Bistum
	 * @ORM\ManyToOne(inversedBy="bands")
	 */
	protected $bistum;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\OneToMany(mappedBy="band")
	 */
	public $klosters;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Bistum
	 */
	public function getBistum() {
		return $this->bistum;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function setBistum(\Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum) {
		$this->bistum = $bistum;
	}

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
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BandHasUrl>
	 * @ORM\OneToMany(mappedBy="band", cascade={"all"})
	 */
	protected $bandHasUrls;

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

	public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

}
?>