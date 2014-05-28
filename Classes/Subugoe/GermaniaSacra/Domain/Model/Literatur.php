<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Literatur {

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
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $citekey;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $beschreibung;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur
	 * @ORM\OneToMany(mappedBy="literatur")
	 */
	protected $klosterHasLiteraturs;

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
	public function getCitekey() {
		return $this->citekey;
	}

	/**
	 * @param string $citekey
	 * @return void
	 */
	public function setCitekey($citekey) {
		$this->citekey = $citekey;
	}

	/**
	 * @return string
	 */
	public function getBeschreibung() {
		return $this->beschreibung;
	}

	/**
	 * @param string $beschreibung
	 * @return void
	 */
	public function setBeschreibung($beschreibung) {
		$this->beschreibung = $beschreibung;
	}
	
	public function __toString()
	{
	  return $this->getCitekey();
	}

	public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

}
?>