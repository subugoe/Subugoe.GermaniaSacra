<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Kloster
{
    /**
     * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    /**
     * @var \TYPO3\Flow\Configuration\ConfigurationManager
     * @Flow\Inject
     */
    protected $configurationManager;

    /**
     * @var int
     */
    protected $uid;

    /**
     * @var int
     * @ORM\Column(nullable=true)
     */
    protected $kloster_id;

    /**
     * @Flow\Validate(type="Text")
     * @var string
     */
    protected $kloster;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $patrozinium;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $bemerkung;

    /**
     * @var string
     * @ORM\Column(length=45, nullable=true)
     */
    protected $band_seite;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text_gs_band;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $bearbeitungsstand;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterstandort>
     * @ORM\OneToMany(mappedBy="kloster", cascade={"all"})
     * @ORM\OrderBy({"von_von" = "ASC", "von_bis" = "ASC", "bis_von" = "ASC", "bis_bis" = "ASC"})
     */
    protected $klosterstandorts;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterorden>
     * @ORM\OneToMany(mappedBy="kloster", cascade={"all"})
     * @ORM\OrderBy({"von_von" = "ASC", "von_bis" = "ASC", "bis_von" = "ASC", "bis_bis" = "ASC"})
     */
    protected $klosterordens;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus
     * @ORM\ManyToOne(inversedBy="klosters")
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $bearbeitungsstatus;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter
     * @ORM\ManyToOne(inversedBy="klosters")
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $bearbeiter;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus
     * @ORM\ManyToOne(inversedBy="klosters")
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $personallistenstatus;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Band
     * @ORM\ManyToOne(inversedBy="klosters")
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=true)
     */
    protected $band;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl>
     * @ORM\OneToMany(mappedBy="kloster", cascade={"all"})
     */
    protected $klosterHasUrls;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur>
     * @ORM\OneToMany(mappedBy="kloster", cascade={"all"})
     */
    protected $klosterHasLiteraturs;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $creationDate;

    /**
     * @var \DateTime
     * @ORM\Column(nullable=true)
     */
    protected $changedDate;

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterstandort>
     */
    public function getKlosterstandorts()
    {
        return $this->klosterstandorts;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $klosterstandorts
     */
    public function setKlosterstandorts(\Doctrine\Common\Collections\Collection $klosterstandorts)
    {
        foreach ($klosterstandorts as $klosterstandort) {
            $klosterstandort->setKloster($this);
        }

        $this->klosterstandorts = $klosterstandorts;
    }

    public function removeKlosterstandorts($klosterstandorts)
    {
        foreach ($klosterstandorts as $klosterstandort) {
            $klosterstandort->removeElement($klosterstandort);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterorden>
     */
    public function getKlosterordens()
    {
        return $this->klosterordens;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $klosterordens
     */
    public function setKlosterordens(\Doctrine\Common\Collections\Collection $klosterordens)
    {
        foreach ($klosterordens as $klosterorden) {
            $klosterorden->setKloster($this);
        }

        $this->klosterordens = $klosterordens;
    }

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
     * @return int
     */
    public function getKloster_id()
    {
        return $this->kloster_id;
    }

    /**
     * @param int $kloster_id
     */
    public function setKloster_id($kloster_id)
    {
        $this->kloster_id = $kloster_id;
    }

    /**
     * @return string
     */
    public function getKloster()
    {
        return $this->kloster;
    }

    /**
     * @param string $kloster
     */
    public function setKloster($kloster)
    {
        $this->kloster = $kloster;
    }

    /**
     * @return string
     */
    public function getPatrozinium()
    {
        return $this->patrozinium;
    }

    /**
     * @param string $patrozinium
     */
    public function setPatrozinium($patrozinium)
    {
        $this->patrozinium = $patrozinium;
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
     * @return string
     */
    public function getBand_seite()
    {
        return $this->band_seite;
    }

    /**
     * @param string $band_seite
     */
    public function setBand_seite($band_seite)
    {
        $this->band_seite = $band_seite;
    }

    /**
     * @return string
     */
    public function getText_gs_band()
    {
        return $this->text_gs_band;
    }

    /**
     * @param string $text_gs_band
     */
    public function setText_gs_band($text_gs_band)
    {
        $this->text_gs_band = $text_gs_band;
    }

    /**
     * @return string
     */
    public function getBearbeitungsstand()
    {
        return $this->bearbeitungsstand;
    }

    /**
     * @param string $bearbeitungsstand
     */
    public function setBearbeitungsstand($bearbeitungsstand)
    {
        $this->bearbeitungsstand = $bearbeitungsstand;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus
     */
    public function getBearbeitungsstatus()
    {
        return $this->bearbeitungsstatus;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatus
     */
    public function setBearbeitungsstatus($bearbeitungsstatus)
    {
        $this->bearbeitungsstatus = $bearbeitungsstatus;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter
     */
    public function getBearbeiter()
    {
        return $this->bearbeiter;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter $bearbeiter
     */
    public function setBearbeiter($bearbeiter)
    {
        $this->bearbeiter = $bearbeiter;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus
     */
    public function getPersonallistenstatus()
    {
        return $this->personallistenstatus;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $personallistenstatus
     */
    public function setPersonallistenstatus($personallistenstatus)
    {
        $this->personallistenstatus = $personallistenstatus;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Band
     */
    public function getBand()
    {
        return $this->band;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Band $band
     */
    public function setBand($band)
    {
        $this->band = $band;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl>
     */
    public function getKlosterHasUrls()
    {
        return $this->klosterHasUrls;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $klosterHasUrls
     */
    public function setKlosterHasUrls(\Doctrine\Common\Collections\Collection $klosterHasUrls)
    {
        foreach ($klosterHasUrls as $klosterHasUrl) {
            $klosterHasUrl->setKloster($this);
        }

        $this->klosterHasUrls = $klosterHasUrls;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur>
     */
    public function getKlosterHasLiteraturs()
    {
        return $this->klosterHasLiteraturs;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $klosterHasLiteraturs
     */
    public function setKlosterHasLiteraturs(\Doctrine\Common\Collections\Collection $klosterHasLiteraturs)
    {
        foreach ($klosterHasLiteraturs as $klosterHasLiteratur) {
            $klosterHasLiteratur->setKloster($this);
        }

        $this->klosterHasLiteraturs = $klosterHasLiteraturs;
    }

    /**
    * @ORM\PrePersist
    */
    public function prePersist()
    {
        if (!isset($this->creationDate)) {
            $this->setCreationDate(new \DateTime());
        } else {
            $this->setCreationDate($this->creationDate);
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate(\DateTime $creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setChangedDate(new \DateTime());
    }

    /**
     * @return \DateTime
     */
    public function getChangedDate()
    {
        return $this->changedDate;
    }

    /**
     * @param \DateTime $changedDate
     */
    public function setChangedDate(\DateTime $changedDate)
    {
        $this->changedDate = $changedDate;
    }

    public function __toString()
    {
        return $this->getKloster();
    }

    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
