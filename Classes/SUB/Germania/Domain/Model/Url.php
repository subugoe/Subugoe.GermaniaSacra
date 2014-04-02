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
class Url {

	/**
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE") 
	 */
	protected $uid;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $bemerkung;

	/**
	 * @var \SUB\Germania\Domain\Model\Urltyp
	 * @ORM\ManyToOne(inversedBy="urls")
	 */
	protected $urltyp;

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
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 * @return void
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getBemerkung() {
		return $this->bemerkung;
	}

	/**
	 * @param string $bemerkung
	 * @return void
	 */
	public function setBemerkung($bemerkung) {
		$this->bemerkung = $bemerkung;
	}

	/**
	 * @return \SUB\Germania\Domain\Model\Urltyp
	 */
	public function getUrltyp() {
		return $this->urltyp;
	}

	/**
	 * @param \SUB\Germania\Domain\Model\Urltyp $urltyp
	 * @return void
	 */
	public function setUrltyp($urltyp) {
		$this->urltyp = $urltyp;
	}
	

	
	
	public function __toString()
	{
	  return $this->getUrl();
	}
}
?>