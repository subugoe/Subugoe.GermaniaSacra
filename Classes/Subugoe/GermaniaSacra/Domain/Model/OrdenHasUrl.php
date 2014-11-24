<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class OrdenHasUrl {

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Orden
	 * @ORM\ManyToOne(inversedBy="ordenHasUrls")
	 * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
	 */
	protected $orden;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Url
	 * @ORM\ManyToOne
	 * @ORM\JoinColumn(onDelete="NO ACTION", nullable=false)
	 */
	protected $url;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Orden
	 */
	public function getOrden() {
		return $this->orden;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Orden $orden
	 * @return void
	 */
	public function setOrden(\Subugoe\GermaniaSacra\Domain\Model\Orden $orden) {
		$this->orden = $orden;
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