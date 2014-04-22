<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

/*                                                                        *

 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class KlosterRepository extends Repository {

	// add customized methods here
	
	public function findKlosters() {
	    $query = $this->createQuery();     
		$query->setLimit(10);
		return $query->execute();
	}
}
?>