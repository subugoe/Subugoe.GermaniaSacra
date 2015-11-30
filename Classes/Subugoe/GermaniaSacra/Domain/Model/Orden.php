<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Orden
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
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterorden>
     * @ORM\OneToMany(mappedBy="orden", cascade={"all"})
     */
    protected $klosterordens;

    /**
     * @var string
     */
    protected $orden;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $ordo;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $symbol;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $graphik;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp
     * @ORM\ManyToOne(inversedBy="ordens")
     * @ORM\JoinColumn(onDelete="NO ACTION")
     */
    protected $ordenstyp;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\OrdenHasUrl>
     * @ORM\OneToMany(mappedBy="orden", cascade={"all"})
     */
    protected $ordenHasUrls;

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
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * @param string $orden
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    /**
     * @return string
     */
    public function getOrdo()
    {
        return $this->ordo;
    }

    /**
     * @param string $ordo
     */
    public function setOrdo($ordo)
    {
        $this->ordo = $ordo;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getGraphik()
    {
        return $this->graphik;
    }

    /**
     * @param string $graphik
     */
    public function setGraphik($graphik)
    {
        $this->graphik = $graphik;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp
     */
    public function getOrdenstyp()
    {
        return $this->ordenstyp;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Ordenstyp $ordenstyp
     */
    public function setOrdenstyp($ordenstyp)
    {
        $this->ordenstyp = $ordenstyp;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\OrdenHasUrl>
     */
    public function getOrdenHasUrls()
    {
        return $this->ordenHasUrls;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $ordenHasUrls
     */
    public function setOrdenHasUrls(\Doctrine\Common\Collections\Collection $ordenHasUrls)
    {
        foreach ($ordenHasUrls as $ordenHasUrl) {
            $ordenHasUrl->setOrden($this);
        }

        $this->ordenHasUrls = $ordenHasUrls;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getOrden();
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
