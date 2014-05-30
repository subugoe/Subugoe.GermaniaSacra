<?php
namespace Subugoe\GermaniaSacra\Domain\Model;



use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class BistumHasUrl {

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Bistum
	 * @ORM\ManyToOne(inversedBy="bistumHasUrls")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $bistum;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Url
	 * @ORM\ManyToOne(inversedBy="bistumHasUrls")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $url;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Bistum
	 */
	public function getBistum() {
		return $this->bistum;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum
	 * @return void
	 */
	public function setBistum(\Subugoe\GermaniaSacra\Domain\Model\Bistum $bistum) {
		$this->bistum = $bistum;
	}


	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Url $url
	 * @return void
	 */
	public function setUrl(\Subugoe\GermaniaSacra\Domain\Model\Url $url) {
		$this->url = $url;
	}

}
?>