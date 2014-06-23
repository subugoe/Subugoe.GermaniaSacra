<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;



use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class KlosterRepository extends Repository {

	public function findKlosters($offset=0, $limit=10) {
	    $query = $this->createQuery();
		$query->setOffset($offset);
		$query->setLimit($limit);
		return $query->execute();
	}

}
?>