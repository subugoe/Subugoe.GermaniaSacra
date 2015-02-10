<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class OrdenstypRepository extends Repository {

	/*
	 * Returns a limited number of Ordenstyp entities
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfOrdenstyp($offset, $limit, $orderings) {
	    $query = $this->createQuery()
			    ->setOrderings($orderings)
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

	/*
	 * Returns the number of Ordenstyp entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->createQuery()->count();
	}

}
?>