<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Band
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
     * @var \Subugoe\GermaniaSacra\Domain\Model\Bistum
     * @ORM\ManyToOne
     * @ORM\JoinColumn(onDelete="NO ACTION")
     */
    protected $bistum;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Kloster>
     * @ORM\OneToMany(mappedBy="band", cascade={"all"})
     */
    protected $klosters;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $nummer;

    /**
     * @var int
     */
    protected $sortierung;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $titel;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BandHasUrl>
     * @ORM\OneToMany(mappedBy="band", cascade={"all"})
     */
    protected $bandHasUrls;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $kurztitel;

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
     * @return \Subugoe\GermaniaSacra\Domain\Model\Bistum
     */
    public function getBistum()
    {
        return $this->bistum;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
     */
    public function setBistum(\Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum)
    {
        $this->bistum = $bistum;
    }

    /**
     * @return string
     */
    public function getNummer()
    {
        return $this->nummer;
    }

    /**
     * @param string $nummer
     */
    public function setNummer($nummer)
    {
        $this->nummer = $nummer;
    }

    /**
     * @return int
     */
    public function getSortierung()
    {
        return $this->sortierung;
    }

    /**
     * @param string $sortierung
     */
    public function setSortierung($sortierung)
    {
        $this->sortierung = $sortierung;
    }

    /**
     * @return string
     */
    public function getTitel()
    {
        return $this->titel;
    }

    /**
     * @param string $titel
     */
    public function setTitel($titel)
    {
        $this->titel = $titel;
    }

    /**
     * @return string
     */
    public function getKurztitel()
    {
        return $this->kurztitel;
    }

    /**
     * @param string $kurztitel
     */
    public function setKurztitel($kurztitel)
    {
        $this->kurztitel = $kurztitel;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BandHasUrl>
     */
    public function getBandHasUrls()
    {
        return $this->bandHasUrls;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $bandHasUrls
     */
    public function setBandHasUrls(\Doctrine\Common\Collections\Collection $bandHasUrls)
    {
        foreach ($bandHasUrls as $bandHasUrl) {
            $bandHasUrl->setBand($this);
        }

        $this->bandHasUrls = $bandHasUrls;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitel();
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
