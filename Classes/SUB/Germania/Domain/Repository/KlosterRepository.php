<?php
namespace SUB\Germania\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "SUB.Germania".          *
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