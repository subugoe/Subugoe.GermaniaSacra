<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Url
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
    protected $url;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $bemerkung;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Urltyp
     * @ORM\ManyToOne(inversedBy="urls")
     * @ORM\JoinColumn(onDelete="NO ACTION")
     */
    protected $urltyp;

    /**
     * @var \Doctrine\Common\Collections\Collection<\Subugoe\GermaniaSacra\Domain\Model\KlosterHasUrl>
     * @ORM\OneToMany(mappedBy="url", cascade={"all"})
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $klosterHasUrls;

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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return \Subugoe\GermaniaSacra\Domain\Model\Urltyp
     */
    public function getUrltyp()
    {
        return $this->urltyp;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Urltyp $urltyp
     */
    public function setUrltyp($urltyp)
    {
        $this->urltyp = $urltyp;
    }

    public function __toString()
    {
        return $this->getUrl();
    }

    public function getUUID()
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }
}
