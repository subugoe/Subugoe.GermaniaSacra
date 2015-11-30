<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Land
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
    * @var int
    * @ORM\Column(nullable=true)
    */
    protected $ist_in_deutschland = false;

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
    public function getLand()
    {
        return $this->land;
    }

    /**
    * @param string $status
    */
    public function setLand($land)
    {
        $this->land = $land;
    }

    /**
    * @return int
    */
    public function getIst_in_deutschland()
    {
        return $this->ist_in_deutschland;
    }

    /**
    * @param string $status
    */
    public function setIst_in_deutschland($ist_in_deutschland)
    {
        $this->ist_in_deutschland = $ist_in_deutschland;
    }

    public function __toString()
    {
        return $this->getLand();
    }

    /**
    * @param void
    * @return string
    */
    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
