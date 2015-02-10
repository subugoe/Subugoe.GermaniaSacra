<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class BearbeitungsstatusRepository extends Repository {

	/*
	 * Returns a limited number of Bearbeitungsstatus entities
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfBearbeitungsstatus($offset, $limit, $orderings) {
	    $query = $this->createQuery()
			    ->setOrderings($orderings)
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

	public function findLastEntry($offset=0, $limit=1) {
		$query = $this->createQuery()
				->setOffset($offset)
				->setLimit($limit)
				->setOrderings(array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING));
		return $query->execute();
	}

	/*
	 * Returns the number of Bearbeitungsstatus entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->createQuery()->count();
	}

}
?>