<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;



use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class BandRepository extends Repository {

	/**
	 * Finds bands
	 *
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The ort
	 */
	public function findBands() {
		$query = $this->createQuery();
		return $query->matching($query->logicalNot($query->like('nummer', 'keine Angabe')))
				->setOrderings(array('sortierung' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING))
				->execute();
	}

}
?>