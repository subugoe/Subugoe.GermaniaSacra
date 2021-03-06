<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Bistum
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
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Ort>
     * @ORM\OneToMany(mappedBy="bistum", cascade={"all"})
     */
    protected $orts;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
     * @ORM\OneToOne
     * @ORM\JoinColumn(onDelete="NO ACTION")
     * @ORM\Column(nullable=true)
     */
    protected $ort;

    /**
     * @var string
     */
    protected $bistum;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $kirchenprovinz;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $bemerkung;

    /**
     * @var int
     * @ORM\Column(nullable=true)
     */
    protected $ist_erzbistum;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $shapefile;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl>
     * @ORM\OneToMany(mappedBy="bistum")
     */
    protected $bistumHasUrls;

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
    public function getBistum()
    {
        return $this->bistum;
    }

    /**
     * @param string $bistum
     */
    public function setBistum($bistum)
    {
        $this->bistum = $bistum;
    }

    /**
     * @return string
     */
    public function getKirchenprovinz()
    {
        return $this->kirchenprovinz;
    }

    /**
     * @param string $kirchenprovinz
     */
    public function setKirchenprovinz($kirchenprovinz)
    {
        $this->kirchenprovinz = $kirchenprovinz;
    }

    /**
     * @return string
     */
    public function getBemerkung()
    {
        return $this->bemerkung;
    }

    /**
     * @param string $bemerkung
     */
    public function setBemerkung($bemerkung)
    {
        $this->bemerkung = $bemerkung;
    }

    /**
     * @return int
     */
    public function getIst_erzbistum()
    {
        return $this->ist_erzbistum;
    }

    /**
     * @param string $ist_erzbistum
     */
    public function setIst_erzbistum($ist_erzbistum)
    {
        $this->ist_erzbistum = $ist_erzbistum;
    }

    /**
     * @return string
     */
    public function getShapefile()
    {
        return $this->shapefile;
    }

    /**
     * @param string $shapefile
     */
    public function setShapefile($shapefile)
    {
        $this->shapefile = $shapefile;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Ort
     */
    public function getOrt()
    {
        return $this->ort;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
     */
    public function setOrt(\Subugoe\GermaniaSacra\Domain\Model\Ort $ort)
    {
        $this->ort = $ort;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\BistumHasUrl>
     */
    public function getBistumHasUrls()
    {
        return $this->bistumHasUrls;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $bistumHasUrls
     */
    public function setBistumHasUrls(\Doctrine\Common\Collections\Collection $bistumHasUrls)
    {
        foreach ($bistumHasUrls as $bistumHasUrl) {
            $bistumHasUrl->setBistum($this);
        }

        $this->bistumHasUrls = $bistumHasUrls;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getBistum();
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
