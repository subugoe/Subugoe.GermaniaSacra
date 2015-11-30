<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Personallistenstatus
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
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Kloster>
     * @ORM\OneToMany(mappedBy="personallistenstatus")
     */
    protected $klosters;

    /**
     * @var string
     */
    protected $name;


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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
