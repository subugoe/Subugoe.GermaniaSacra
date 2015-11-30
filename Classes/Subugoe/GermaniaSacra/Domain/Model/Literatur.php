<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Literatur
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
    public function getCitekey()
    {
        return $this->citekey;
    }

    /**
     * @param string $citekey
     */
    public function setCitekey($citekey)
    {
        $this->citekey = $citekey;
    }

    /**
     * @return string
     */
    public function getBeschreibung()
    {
        return $this->beschreibung;
    }

    /**
     * @param string $beschreibung
     */
    public function setBeschreibung($beschreibung)
    {
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
