<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class UrltypRepository extends Repository {

	/*
	 * Returns a limited number of Urltyp entities
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfUrltyp($offset, $limit) {
	    $query = $this->createQuery()
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

}
?>