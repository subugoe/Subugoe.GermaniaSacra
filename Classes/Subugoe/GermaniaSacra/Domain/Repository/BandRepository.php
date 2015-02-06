<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class BandRepository extends Repository {

	/*
	 * Returns a limited number of Band entities
	 * @param $offset The offset
	 * @param $limit The limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfBand($offset, $limit) {
	    $query = $this->createQuery();
		return $query->matching($query->logicalNot($query->like('nummer', 'keine Angabe')))
				->setOrderings(array('sortierung' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING))
				->setOffset($offset)
				->setLimit($limit)
				->execute();
	}

}
?>