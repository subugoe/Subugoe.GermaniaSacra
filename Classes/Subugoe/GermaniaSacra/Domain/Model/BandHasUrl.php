<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class BandHasUrl
{
    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Band
     * @ORM\ManyToOne(inversedBy="bandHasUrls")
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $band;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Url
     * @ORM\ManyToOne
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $url;

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
    public function setBand(\Subugoe\GermaniaSacra\Domain\Model\Band $band)
    {
        $this->band = $band;
    }

    /**
     * @return \Subugoe\GermaniaSacra\Domain\Model\Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Url $url
     */
    public function setUrl(\Subugoe\GermaniaSacra\Domain\Model\Url $url)
    {
        $this->url = $url;
    }
}
