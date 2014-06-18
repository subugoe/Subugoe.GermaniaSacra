<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Orden {

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
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Klosterorden
	 * @ORM\OneToMany(mappedBy="orden")
	 */
	protected $klosterordens;

	/**
	 * @var string
	 */
	protected $orden;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $ordo;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $symbol;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $graphik;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp
	 * @ORM\ManyToOne(inversedBy="Ordens")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $ordenstyp;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\OrdenHasUrl>
	 * @ORM\OneToMany(mappedBy="orden", cascade={"all"})
	 */
	protected $ordenHasUrls;

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
	public function getOrden() {
		return $this->orden;
	}

	/**
	 * @param string $orden
	 * @return void
	 */
	public function setOrden($orden) {
		$this->orden = $orden;
	}

	/**
	 * @return string
	 */
	public function getOrdo() {
		return $this->ordo;
	}

	/**
	 * @param string $ordo
	 * @return void
	 */
	public function setOrdo($ordo) {
		$this->ordo = $ordo;
	}

	/**
	 * @return string
	 */
	public function getSymbol() {
		return $this->symbol;
	}

	/**
	 * @param string $symbol
	 * @return void
	 */
	public function setSymbol($symbol) {
		$this->symbol = $symbol;
	}

	/**
	 * @return string
	 */
	public function getGraphik() {
		return $this->graphik;
	}

	/**
	 * @param string $graphik
	 * @return void
	 */
	public function setGraphik($graphik) {
		$this->graphik = $graphik;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp
	 */
	public function getOrdenstyp() {
		return $this->ordenstyp;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp $ordenstyp
	 * @return void
	 */
	public function setOrdenstyp($ordenstyp) {
		$this->ordenstyp = $ordenstyp;
	}




	/**
	 * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\OrdenHasUrl>
	 */
	public function getOrdenHasUrls() {
		return $this->ordenHasUrls;
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $ordenHasUrls
	 * @return void
	 */
	public function setOrdenHasUrls(\Doctrine\Common\Collections\Collection $ordenHasUrls) {

		foreach ($ordenHasUrls as $ordenHasUrl) {
			$ordenHasUrl->setOrden($this);
		}

		$this->ordenHasUrls = $ordenHasUrls;
	}





	public function __toString() {
		return $this->getOrden();
	}

	public function getUUID() {
		return $this->persistenceManager->getIdentifierByObject($this);
	}

}

?>