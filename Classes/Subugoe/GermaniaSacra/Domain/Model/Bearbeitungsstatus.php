<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bearbeitungsstatus {

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
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Kloster>
	 * @ORM\OneToMany(mappedBy="bearbeitungsstatus")
	 */
	protected $klosters;

	/**
	 * @var string
	 */
	protected $name;

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
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getName();
	}

	/**
	 * Returns the persistence object identifier of the object
	 * @return string
	 */
	public function getUUID() {
		return $this->persistenceManager->getIdentifierByObject($this);
	}
}
?>