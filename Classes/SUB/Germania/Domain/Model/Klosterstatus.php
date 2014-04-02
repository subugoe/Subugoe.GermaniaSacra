<?php
namespace SUB\Germania\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Klosterstatus {

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var \SUB\Germania\Domain\Model\Klosterorden>
	 * @ORM\OneToMany(mappedBy="klosterstatus")
	 */
	protected $klosterordens;

	/**
	 * @var string
	 */
	protected $status;


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
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	public function __toString()
	{
	  return $this->getStatus();
	}
}
?>