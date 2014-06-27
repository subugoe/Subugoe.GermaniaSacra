<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;



use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class KlosterRepository extends Repository {

	public function findKlosters($offset=0, $limit=10) {
	    $query = $this->createQuery()
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

	public function findLastEntry($offset=0, $limit=1) {
		$query = $this->createQuery()
				->setOffset($offset)
				->setLimit($limit)
				->setOrderings(array('kloster_id' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING));
		return $query->execute();
	}
}
?>