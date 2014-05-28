<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Kloster {

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
	 * @var integer
	 * @ORM\Column(columnDefinition="INT(11) NOT NULL AUTO_INCREMENT UNIQUE")
	 */
	protected $uid;

	/**
	 * @var integer
	 */
	protected $kloster_id;

	/**
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
	 * @ORM\Column(nullable=true)
	 */
	protected $bemerkung;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $band_seite;
	
	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $text_gs_band;

	/**
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $bearbeitungsstand;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterstandort>
	 * @ORM\OneToMany(mappedBy="kloster", cascade={"all"})
	 */
	protected $klosterstandorts;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterorden>
	 * @ORM\OneToMany(mappedBy="kloster", cascade={"all"})
	 */
	protected $klosterordens;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus
	 * @ORM\ManyToOne(inversedBy="klosters")
	 */
	protected $bearbeitungsstatus;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter
	 * @ORM\ManyToOne(inversedBy="klosters")
	 */
	protected $bearbeiter;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus
	 * @ORM\ManyToOne(inversedBy="klosters")
	 */
	protected $personallistenstatus;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Band
	 * @ORM\ManyToOne(inversedBy="klosters")
	 * @ORM\Column(nullable=true)
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
	public function getKlosterstandorts() {
		return $this->klosterstandorts;
	}
 
	/**
	* @param \Doctrine\Common\Collections\Collection $klosterstandorts
	* @return void
	*/
	public function setKlosterstandorts(\Doctrine\Common\Collections\Collection $klosterstandorts) {
		
		foreach ($klosterstandorts as $klosterstandort){$klosterstandort->setKloster($this);}
	
		$this->klosterstandorts = $klosterstandorts;
	}

	public function removeKlosterstandorts($klosterstandorts) {
		
		foreach ($klosterstandorts as $klosterstandort){
			$klosterstandort->removeElement($klosterstandort);
		}
	}

	/**
	* @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\Klosterorden>
	*/
	public function getKlosterordens() {
		return $this->klosterordens;
	}
 
	/**
	* @param \Doctrine\Common\Collections\Collection $klosterordens
	* @return void
	*/
	public function setKlosterordens(\Doctrine\Common\Collections\Collection $klosterordens) {
		
		foreach ($klosterordens as $klosterorden){$klosterorden->setKloster($this);}
	
		$this->klosterordens = $klosterordens;
	}
	
	/**
	 * @return integer
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * @param integer $uid
	 * @return void
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * @return integer
	 */
	public function getKloster_id() {
		return $this->kloster_id;
	}

	/**
	 * @param integer $kloster_id
	 * @return void
	 */
	public function setKloster_id($kloster_id) {
		$this->kloster_id = $kloster_id;
	}

	/**
	 * @return string
	 */
	public function getKloster() {
		return $this->kloster;
	}

	/**
	 * @param string $kloster
	 * @return void
	 */
	public function setKloster($kloster) {
		$this->kloster = $kloster;
	}

	/**
	 * @return string
	 */
	public function getPatrozinium() {
		return $this->patrozinium;
	}

	/**
	 * @param string $patrozinium
	 * @return void
	 */
	public function setPatrozinium($patrozinium) {
		$this->patrozinium = $patrozinium;
	}

	/**
	 * @return string
	 */
	public function getBemerkung() {
		return $this->bemerkung;
	}

	/**
	 * @param string $bemerkung
	 * @return void
	 */
	public function setBemerkung($bemerkung) {
		$this->bemerkung = $bemerkung;
	}

	/**
	 * @return string
	 */
	public function getBand_seite() {
		return $this->band_seite;
	}

	/**
	 * @param string $band_seite
	 * @return void
	 */
	public function setBand_seite($band_seite) {
		$this->band_seite = $band_seite;
	}

	/**
	 * @return string
	 */
	public function getText_gs_band() {
		return $this->text_gs_band;
	}

	/**
	 * @param string $text_gs_band
	 * @return void
	 */
	public function setText_gs_band($text_gs_band) {
		$this->text_gs_band = $text_gs_band;
	}

	/**
	 * @return string
	 */
	public function getBearbeitungsstand() {
		return $this->bearbeitungsstand;
	}

	/**
	 * @param string $bearbeitungsstand
	 * @return void
	 */
	public function setBearbeitungsstand($bearbeitungsstand) {
		$this->bearbeitungsstand = $bearbeitungsstand;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus
	 */
	public function getBearbeitungsstatus() {
		return $this->bearbeitungsstatus;
	}
	
	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeitungsstatus $bearbeitungsstatus
	 * @return void
	 */
	public function setBearbeitungsstatus($bearbeitungsstatus) {
		$this->bearbeitungsstatus = $bearbeitungsstatus;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter
	 */
	public function getBearbeiter() {
		return $this->bearbeiter;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter $bearbeiter
	 * @return void
	 */
	public function setBearbeiter($bearbeiter) {
		$this->bearbeiter = $bearbeiter;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus
	 */
	public function getPersonallistenstatus() {
		return $this->personallistenstatus;
	}
	
	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus $personallistenstatus
	 * @return void
	 */
	public function setPersonallistenstatus($personallistenstatus) {
		$this->personallistenstatus = $personallistenstatus;
	}

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Band
	 */
	public function getBand() {
		return $this->band;
	}
	
	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Band $band
	 * @return void
	 */
	public function setBand($band) {
		$this->band = $band;
	}

	/**
	* @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl>
	*/
	public function getKlosterHasUrls() {
		return $this->klosterHasUrls;
	}

	/**
	* @param \Doctrine\Common\Collections\Collection $klosterHasUrls
	* @return void
	*/
	public function setKlosterHasUrls(\Doctrine\Common\Collections\Collection $klosterHasUrls) {

		foreach ($klosterHasUrls as $klosterHasUrl){$klosterHasUrl->setKloster($this);}

		$this->klosterHasUrls = $klosterHasUrls;
	}

	/**
	* @return \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasLiteratur>
	*/
	public function getKlosterHasLiteraturs() {
		return $this->klosterHasLiteraturs;
	}

	/**
	* @param \Doctrine\Common\Collections\Collection $klosterHasLiteraturs
	* @return void
	*/
	public function setKlosterHasLiteraturs(\Doctrine\Common\Collections\Collection $klosterHasLiteraturs) {

		foreach ($klosterHasLiteraturs as $klosterHasLiteratur){$klosterHasLiteratur->setKloster($this);}

		$this->klosterHasLiteraturs = $klosterHasLiteraturs;
	}

	/**
	* @ORM\PrePersist
	* @return void
	*/
//	public function prePersist() {
//		$this->setCreationDate(new \DateTime());
//	}

	/**
	 * @return \DateTime
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * @param \DateTime $creationDate
	 * @return void
	 */
	public function setCreationDate(\DateTime $creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	* @ORM\PreUpdate
	* @return void
	*/
	public function preUpdate() {
		$this->setChangedDate(new \DateTime());
	}

	/**
	 * @return \DateTime
	 */
	public function getChangedDate() {
		return $this->changedDate;
	}

	/**
	 * @param \DateTime $changedDate
	 * @return void
	 */
	public function setChangedDate(\DateTime $changedDate) {
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
?>