<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class OrtHasUrl {

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Ort
	 * @ORM\ManyToOne(inversedBy="ortHasUrls")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $ort;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Url
	 * @ORM\ManyToOne(inversedBy="ortHasUrls")
	 * @ORM\JoinColumn(onDelete="NO ACTION")
	 */
	protected $url;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Ort
	 */
	public function getOrt() {
		return $this->ort;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Ort $ort
	 * @return void
	 */
	public function setOrt(\Subugoe\GermaniaSacra\Domain\Model\Ort $ort) {
		$this->ort = $ort;
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