<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;



use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;
use Subugoe\GermaniaSacra\Domain\Model\Kloster as Kloster;
use TYPO3\Flow\Reflection\ObjectAccess;
use Doctrine\ORM\Query\Expr;

/**
 * @Flow\Scope("singleton")
 */
class KlosterRepository extends Repository {

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

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

	public function findKlosterByWildCard($alle) {

		$query = $this->createQuery();
	/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('k.Persistence_Object_Identifier')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'k');

		if (!empty($alle)) {
			$alle = "%" . trim($alle) . "%";
			$alle = (string)$alle;
			$queryBuilder->innerJoin('k.klosterstandorts', 's')
						->innerJoin('s.ort', 'o')
						->innerJoin('k.bearbeitungsstatus', 'b')
						->innerJoin('k.band', 'band')
						->innerJoin('o.bistum', 'bistum')
						->innerJoin('o.land', 'land')
						->innerJoin('k.klosterordens', 'klosterorden')
						->innerJoin('klosterorden.orden', 'orden')

						->where('k.kloster_id LIKE :alle OR
								k.kloster LIKE :alle OR
								k.patrozinium LIKE :alle OR
								s.von_von LIKE :alle OR
								s.bis_bis LIKE :alle OR
								s.breite LIKE :alle OR
								s.laenge LIKE :alle OR
								band.nummer LIKE :alle OR
								bistum.bistum LIKE :alle OR
								o.ort LIKE :alle OR
								orden.orden LIKE :alle OR
								land.land LIKE :alle')
							->setParameter('alle', $alle);

		}

		return $query->execute();
	}

}
?>