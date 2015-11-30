<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bearbeiter
{
    /**
     * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    /**
     * @var int
     * @ORM\Column(nullable=true)
     */
    protected $uid;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Kloster>
     * @ORM\OneToMany(mappedBy="bearbeiter")
     */
    protected $klosters;

    /**
     * @var string
     */
    protected $bearbeiter;

    /**
     * @var \TYPO3\Flow\Security\Account
     * @ORM\ManyToOne(cascade={"all"})
     */
    protected $account;

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getBearbeiter()
    {
        return $this->bearbeiter;
    }

    /**
     * @param string $bearbeiter
     */
    public function setBearbeiter($bearbeiter)
    {
        $this->bearbeiter = $bearbeiter;
    }

    /**
     * Sets (and adds if necessary) the account.
     *
     * @param \TYPO3\Flow\Security\Account $account
     */
    public function setAccount(\TYPO3\Flow\Security\Account $account)
    {
        $this->account = $account;
    }

    /**
     * Returns the account, if one has been defined.
     *
     * @return \TYPO3\Flow\Security\Account $account
     */
    public function getAccount()
    {
        return $this->account;
    }

    public function __toString()
    {
        return $this->getBearbeiter();
    }

    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
