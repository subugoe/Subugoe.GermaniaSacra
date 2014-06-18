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
				->execute();
	}

	/**
	 * Returns a list of Ort entries
	 *
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The ort
	 */
	public function findOrts($offset=0, $limit=10) {
	    $query = $this->createQuery();
		$query->setOffset($offset);
		$query->setLimit($limit);
		return $query->execute();
	}

}

?>