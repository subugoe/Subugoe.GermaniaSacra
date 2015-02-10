<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class OrtRepository extends Repository {

	/**
	 * Finds ort as per entered search string
	 *
	 * @param string $searchString The entered search string
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The ort
	 */
	public function findOrtBySearchString($searchString) {
		$query = $this->createQuery();
		return $query->matching($query->like('ort', $searchString))
				->setOrderings(array('ort' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING))
				->setLimit(20)
				->execute();
	}

	/**
	 * Returns a limited number of Ort entities
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfOrt($offset, $limit) {
	    $query = $this->createQuery()
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

	/*
	 * Returns the number of Ort entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->createQuery()->count();
	}

}

?>