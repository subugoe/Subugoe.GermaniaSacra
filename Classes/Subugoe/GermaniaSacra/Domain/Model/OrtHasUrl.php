<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class OrtHasUrl
{
    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
     * @ORM\ManyToOne(inversedBy="ortHasUrls")
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $ort;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Url
     * @ORM\ManyToOne
     * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
     */
    protected $url;

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
