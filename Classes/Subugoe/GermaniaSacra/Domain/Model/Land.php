<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Land {

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
	* @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Ort>
	* @ORM\OneToMany(mappedBy="land", cascade={"all"})
	* @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
	*/
	protected $orts;

	/**
	* @var string
	*/
	protected $land;

	/**
	* @var integer
	* @ORM\Column(nullable=true)
	*/
	protected $ist_in_deutschland = FALSE;
	
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
	public function getLand() {
		return $this->land;
	}

	/**
	* @param string $status
	* @return void
	*/
	public function setLand($land) {
		$this->land = $land;
	}

	/**
	* @return integer
	*/
	public function getIst_in_deutschland() {
		return $this->ist_in_deutschland;
	}

	/**
	* @param string $status
	* @return void
	*/
	public function setIst_in_deutschland($ist_in_deutschland) {
		$this->ist_in_deutschland = $ist_in_deutschland;
	}

	public function __toString() {
	  return $this->getLand();
	}

	/**
	* @param void
	* @return string
	*/
	public function getUUID() {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
?>