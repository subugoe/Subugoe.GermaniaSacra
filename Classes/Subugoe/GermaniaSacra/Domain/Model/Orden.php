<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

/*                                                                        *

 *                                                                        *
 *                                                                        */

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
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
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
	 */
	protected $ordo;

	/**
	 * @var string
	 */
	protected $symbol;

	/**
	 * @var string
	 */
	protected $graphik;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp
	 * @ORM\ManyToOne(inversedBy="Ordens")
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

	public function __toString()
	{
	  return $this->getOrden();
	}

	public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

}
?>