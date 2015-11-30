<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Ordenstyp
{
    /**
  * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
  * @Flow\Inject
  */
 protected $persistenceManager;

    /**
     * @var int
     * @ORM\Column(nullable=TRUE)
     */
    protected $uid;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Orden>
     * @ORM\OneToMany(mappedBy="ordenstyp")
     */
    protected $ordens;

    /**
     * @var string
     */
    protected $ordenstyp;

    /**
     * @return int
     */
    public function getuid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setuid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getOrdenstyp()
    {
        return $this->ordenstyp;
    }

    /**
     * @param string $ordenstyp
     */
    public function setOrdenstyp($ordenstyp)
    {
        $this->ordenstyp = $ordenstyp;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getOrdenstyp();
    }

    /**
     * Returns the persistence object identifier of the object
     * @return string
     */
    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
