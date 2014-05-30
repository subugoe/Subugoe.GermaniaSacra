<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Land {

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
	 * @ORM\OneToMany(mappedBy="land")
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

	public function __toString()
	{
	  return $this->getLand();
	}
}
?>