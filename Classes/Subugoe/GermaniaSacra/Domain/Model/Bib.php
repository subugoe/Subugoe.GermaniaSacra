<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bib {

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @var integer
	 * @ORM\Column(nullable=TRUE)
	 */
	protected $abstract;

	/**
	 * @var string
	 */
	protected $address;

	/**
	 * @var string
	 */
	protected $affiliation;

	/**
	 * @return string
	 */
	public function getAbstract() {
		return $this->abstract;
	}

	/**
	 * @param string $abstract
	 * @return void
	 */
	public function setAbstract($abstract) {
		$this->abstract = $abstract;
	}

	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param string $address
	 * @return void
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	/**
	 * @return string
	 */
	public function getAffiliation() {
		return $this->affiliation;
	}

	/**
	 * @param string $affiliation
	 * @return void
	 */
	public function setAffiliation($affiliation) {
		$this->affiliation = $affiliation;
	}

	public function __toString() {
		return $this->getAbstract();
	}

	public function getUUID() {
		return $this->persistenceManager->getIdentifierByObject($this);
	}

}

?>