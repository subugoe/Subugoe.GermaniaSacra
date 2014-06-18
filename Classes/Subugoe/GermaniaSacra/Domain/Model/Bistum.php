<?php
namespace Subugoe\GermaniaSacra\Domain\Model;


use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bistum {

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
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
	 * @ORM\OneToMany(mappedBy="bistum")
	 */
	protected $orts;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
	 * @ORM\OneToOne(mappedBy="bistums")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 * @ORM\Column(nullable=true)
	 */
	protected $ort;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Band
	 * @ORM\OneToMany(mappedBy="bistum")
	 */
	protected $bands;

	/**
	 * @var string
	 */
	protected $bistum;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $kirchenprovinz;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $bemerkung;

	/**
	 * @var integer
	 * @ORM\Column(nullable=true)
	 */
	protected $ist_erzbistum;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $shapefile;










	/**
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl>
	 * @ORM\OneToMany(mappedBy="bistum", cascade={"all"})
	 */
	protected $bistumHasUrls;






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
	public function getBistum() {
		return $this->bistum;
	}

	/**
	 * @param string $bistum
	 * @return void
	 */
	public function setBistum($bistum) {
		$this->bistum = $bistum;
	}

	/**
	 * @return string
	 */
	public function getKirchenprovinz() {
		return $this->kirchenprovinz;
	}

	/**
	 * @param string $kirchenprovinz
	 * @return void
	 */
	public function setKirchenprovinz($kirchenprovinz) {
		$this->kirchenprovinz = $kirchenprovinz;
	}

	/**
	 * @return string
	 */
	public function getBemerkung() {
		return $this->bemerkung;
	}

	/**
	 * @param string $symbol
	 * @return void
	 */
	public function setBemerkung($bemerkung) {
		$this->bemerkung = $bemerkung;
	}

	/**
	 * @return integer
	 */
	public function getIst_erzbistum() {
		return $this->ist_erzbistum;
	}

	/**
	 * @param string $ist_erzbistum
	 * @return void
	 */
	public function setIst_erzbistum($ist_erzbistum) {
		$this->ist_erzbistum = $ist_erzbistum;
	}


	/**
	 * @return string
	 */
	public function getShapefile() {
		return $this->shapefile;
	}

	/**
	 * @param string $shapefile
	 * @return void
	 */
	public function setShapefile($shapefile) {
		$this->shapefile = $shapefile;
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
	 * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl>
	 */
	public function getBistumHasUrls() {
		return $this->bistumHasUrls;
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $bistumHasUrls
	 * @return void
	 */
	public function setBistumHasUrls(\Doctrine\Common\Collections\Collection $bistumHasUrls) {

		foreach ($bistumHasUrls as $bistumHasUrl) {
			$bistumHasUrl->setBistum($this);
		}

		$this->bistumHasUrls = $bistumHasUrls;
	}

	public function __toString() {
		return $this->getBistum();
	}

	public function getUUID() {
		return $this->persistenceManager->getIdentifierByObject($this);
	}

}

?>