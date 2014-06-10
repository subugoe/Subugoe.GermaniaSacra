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
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Bistum
	 * @ORM\ManyToOne(inversedBy="bands")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $bistum;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\OneToMany(mappedBy="band")
	 */
	public $klosters;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $nummer;

	/**
	 * @var integer
	 */
	protected $sortierung;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $titel;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BandHasUrl>
	 * @ORM\OneToMany(mappedBy="band", cascade={"all"})
	 */
	protected $bandHasUrls;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
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

	/**
	 * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BandHasUrl>
	 */
	public function getBandHasUrls() {
		return $this->bandHasUrls;
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $bandHasUrls
	 * @return void
	 */
	public function setBandHasUrls(\Doctrine\Common\Collections\Collection $bandHasUrls) {

		foreach ($bandHasUrls as $bandHasUrl) {
			$bandHasUrl->setBand($this);
		}

		$this->bandHasUrls = $bandHasUrls;
	}

	public function __toString() {
		return $this->getTitel();
	}

	public function getUUID() {
		return $this->persistenceManager->getIdentifierByObject($this);
	}

}

?>