<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class KlosterHasUrl {

	/**
	* @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	* @Flow\Inject
	*/
	protected $persistenceManager;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\ManyToOne(inversedBy="klosterHasUrls")
	 */
	protected $kloster;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Url
	 * @ORM\ManyToOne(inversedBy="klosterHasUrls")
	 */
	protected $url;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 */
	public function getKloster() {
		return $this->kloster;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function setKloster(\Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster) {
		$this->kloster = $kloster;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Url $url
	 * @return void
	 */
	public function setUrl(\Subugoe\GermaniaSacra\Domain\Model\Url $url) {
		$this->url = $url;
	}

	public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

}
?>