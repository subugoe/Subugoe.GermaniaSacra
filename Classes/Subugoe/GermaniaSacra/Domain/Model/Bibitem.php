<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bibitem
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
     * @var string
     */
    protected $bibitem;

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
    public function getBibitem()
    {
        return $this->bibitem;
    }

    /**
     * @param string $bibitem
     */
    public function setBibitem($bibitem)
    {
        $this->bibitem = $bibitem;
    }

    public function __toString()
    {
        return $this->getBibitem();
    }

    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
