<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class KlosterstandortRepository extends Repository {

	public function findLastEntry($offset=0, $limit=1) {
		$query = $this->createQuery()
				->setOffset($offset)
				->setLimit($limit)
				->setOrderings(array('uid' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING));
		return $query->execute();
	}

}
?>