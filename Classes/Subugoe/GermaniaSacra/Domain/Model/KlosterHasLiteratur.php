<?php
namespace Subugoe\GermaniaSacra\Domain\Model;

/*                                                                        *

 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class KlosterHasLiteratur {

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 * @ORM\ManyToOne(inversedBy="klosterHasLiteraturs")
	 */
	protected $kloster;

	/**
	 * @var \Subugoe\GermaniaSacra\Domain\Model\Literatur
	 * @ORM\ManyToOne(inversedBy="klosterHasLiteraturs")
	 */
	protected $literatur;

	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Kloster
	 */
	public function getKloster() {
		return $this->kloster;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster
	 * @return void
	 */
	public function setKloster(\Subugoe\GermaniaSacra\Domain\Model\Kloster $kloster) {
		$this->kloster = $kloster;
	}


	/**
	 * @return \Subugoe\GermaniaSacra\Domain\Model\Literatur
	 */
	public function getLiteratur() {
		return $this->literatur;
	}

	/**
	 * @param \Subugoe\GermaniaSacra\Domain\Model\Literatur $literatur
	 * @return void
	 */
	public function setLiteratur(\Subugoe\GermaniaSacra\Domain\Model\Literatur $literatur) {
		$this->literatur = $literatur;
	}



}
?>