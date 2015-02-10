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
	public function getCertainNumberOfBand($offset, $limit, $orderings) {
	    $query = $this->createQuery();
		return $query->matching($query->logicalNot($query->like('nummer', 'keine Angabe')))
				->setOrderings($orderings)
				->setOffset($offset)
				->setLimit($limit)
				->execute();
	}

	/*
	 * Returns the number of Band entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->createQuery()->count();
	}

}
?>