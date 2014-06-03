<?php
namespace Subugoe\GermaniaSacra\Domain\Model;


use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bearbeiter {

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
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\OneToMany(mappedBy="bearbeiter")
	 */
	protected $klosters;

	/**
	 * @var string
	 */
	protected $bearbeiter;

	/**
	 * @return integer
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * @param integer $uid
	 * @return void
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * @return string
	 */
	public function getBearbeiter() {
		return $this->bearbeiter;
	}

	/**
	 * @param string $bearbeiter
	 * @return void
	 */
	public function setBearbeiter($bearbeiter) {
		$this->bearbeiter = $bearbeiter;
	}

	public function __toString() {
		return $this->getBearbeiter();
	}

	public function getUUID() {
		return $this->persistenceManager->getIdentifierByObject($this);
	}

}

?>