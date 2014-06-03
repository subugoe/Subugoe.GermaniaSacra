<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Ordenstyp {

	/**
  * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
  * @Flow\Inject
  */
 protected $persistenceManager;

	/**
	 * @var integer
	 * @ORM\Column(nullable=TRUE)
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Orden
	 * @ORM\OneToMany(mappedBy="ordenstyp")
	 */
	protected $ordens;

	/**
	 * @var string
	 */
	protected $ordenstyp;

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
	public function getOrdenstyp() {
		return $this->ordenstyp;
	}

	/**
	 * @param string $ordenstyp
	 * @return void
	 */
	public function setOrdenstyp($ordenstyp) {
		$this->ordenstyp = $ordenstyp;
	}

	public function __toString()
	{
	  return $this->getOrdenstyp();
	}

	public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

}
?>