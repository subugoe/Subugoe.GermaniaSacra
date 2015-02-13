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

	/*
	 * Searches and returns a limited number of Kloster entities as per search terms
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function searchCertainNumberOfKloster($offset, $limit, $orderings, $searchArr, $mode) {

		$query = $this->createQuery();
	/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('kloster')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'kloster');


		$entity = 'kloster';
		$operator = 'LIKE';

		if (is_array($searchArr) && count($searchArr) > 0) {


			$i = 1;

			foreach ($searchArr as $k => $v) {


				$parameter = $k;

				$searchStr = trim($v);
				$value = '%' . $searchStr . '%';


				$filter = $entity . '.' . $k;

				if ($k === 'bearbeitungsstatus') {
					$queryBuilder->innerJoin('kloster.bearbeitungsstatus', 'bearbeitungsstatus');
					$filter = 'bearbeitungsstatus.name';

					$entity = 'bearbeitungsstatus';

				}

				if ($k === 'ort') {
					$queryBuilder->innerJoin('kloster.klosterstandorts', 'klosterstandort')
								->innerJoin('klosterstandort.ort', 'ort');
					$filter = 'ort.ort';



					if ($orderings[0] === 'ort') {

						$entity = 'ort';

					}

				}

				if ($i === 1) {
					$queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter);
				}
				else {
					$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter);
				}


				$queryBuilder->setParameter($parameter, $value);

				$i++;
			}
		}



		if ($mode === 1) {
			$queryBuilder->orderBy($entity . '.' . $orderings[0], $orderings[1]);
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults($limit);


			return $query->execute();
		}
		else {
			return $query->count();
		}


	}





	public function getCertainNumberOfKloster($offset, $limit, $orderings) {


		$entity = 'kloster';

		$query = $this->createQuery();
		/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('kloster')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'kloster');


		if ($orderings[0] === 'ort') {
			$queryBuilder->innerJoin('kloster.klosterstandorts', 'klosterstandort')
						->innerJoin('klosterstandort.ort', 'ort');
			$entity = 'ort';
		}


		if ($orderings[0] === 'gnd') {
			$queryBuilder->innerJoin('kloster.klosterHasUrls', 'klosterhasurl')
						->innerJoin('klosterhasurl.url', 'url')
						->innerJoin('url.urltyp', 'urltyp');

			$queryBuilder->where('urltyp.name LIKE :gnd')

			->setParameter('gnd', 'GND');

			$entity = 'url';

			$orderings[0] = 'url';
		}


		$queryBuilder->orderBy($entity . '.' . $orderings[0], $orderings[1]);
		$queryBuilder->setFirstResult($offset);
		$queryBuilder->setMaxResults($limit);

		return $query->execute();

	}






//	/*
//	 * Returns a limited number of Kloster entities
//	 * @param integer $offset The select offset
//	 * @param integer $limit The select limit
//	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
//	 */
//	public function getCertainNumberOfKloster($offset, $limit, $orderings) {
//	    $query = $this->createQuery()
//			    ->setOrderings($orderings)
//				->setOffset($offset)
//				->setLimit($limit);
//		return $query->execute();
//	}

	/*
	 * Returns the number of Kloster entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->countAll();
//		return $this->createQuery()->count();
	}

	/*
	 * Returns the last Kloster entity in the table
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function findLastEntry($offset=0, $limit=1) {
		$query = $this->createQuery();
		$query->matching($query->lessThan('kloster_id', 20000))
				->setOrderings(array('kloster_id' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING))
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

	public function findKlosterByWildCard($alle) {

		if (!empty($alle)) {

			$query = $this->createQuery();
		/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
			$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
			$queryBuilder
			->resetDQLParts()
			->select('k.Persistence_Object_Identifier')
			->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'k');

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
								b.name LIKE :alle OR
								s.von_von LIKE :alle OR
								s.bis_bis LIKE :alle OR
								s.breite LIKE :alle OR
								s.laenge LIKE :alle OR
								band.nummer LIKE :alle OR
								bistum.bistum LIKE :alle OR
								o.ort LIKE :alle OR
								orden.orden LIKE :alle OR
								land.land LIKE :alle OR
								land.land LIKE :alle AND land.ist_in_deutschland = :ist_in_deutschland')
						->setParameter('alle', $alle)
						->setParameter('ist_in_deutschland', 1);

				return $query->execute();
		}

	}

	public function findKlosterByAdvancedSearch($searchArr) {

		$query = $this->createQuery();
	/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('kloster.Persistence_Object_Identifier')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'kloster');

		$check = array();
		$parameterArr = array();

		if (is_array($searchArr) && count($searchArr) > 0) {
			foreach ($searchArr as $k => $v) {

				$searchStr = trim($v['text']);
				$filter = $v['filter'];
				$parameter = $v['joinParams']['parameter'];
				$operator = $v['operator'];

				if ($searchStr !== '') {
					if ($operator == 'LIKE' || $operator == 'NOT LIKE') {
						$value = '%' . $searchStr . '%';
					}
					elseif ($operator == 'START') {
						$value = $searchStr . '%';
						$operator = 'LIKE';
					}
					elseif ($operator == 'END') {
						$value = '%' . $searchStr;
						$operator = 'LIKE';
					}
					else {
						$value = $searchStr;
					}
				}
				else {
					$value = Null;
				}

				if ($value === Null) {
					$v['operator'] = 'IS NULL';
				}

				if (isset($v['joinParams']['join']) && is_array($v['joinParams']['join']) && !in_array($v['joinParams']['duplicateJoinCheck'][0],$check)) {
					foreach ($v['joinParams']['join'] as $join) {
						$queryBuilder->innerJoin($join[0] . '.' . $join[1], $join['2']);
					}
				}

				if (isset($v['joinParams']['secondjoin']) && is_array($v['joinParams']['secondjoin']) && !in_array($v['joinParams']['duplicateJoinCheck'][1],$check)) {
					foreach ($v['joinParams']['secondjoin'] as $secondjoin) {
						$queryBuilder->innerJoin($secondjoin[0] . '.' . $secondjoin[1], $secondjoin['2']);
					}
				}

				if (isset($v['joinParams']['thirdjoin']) && is_array($v['joinParams']['thirdjoin']) && !in_array($v['joinParams']['duplicateJoinCheck'][2],$check)) {
					foreach ($v['joinParams']['thirdjoin'] as $thirdjoin) {
						$queryBuilder->innerJoin($thirdjoin[0] . '.' . $thirdjoin[1], $thirdjoin['2']);
					}
				}

				if (in_array($parameter, $parameterArr)) {
					$parameter = $parameter . '_' . $k;
				}

				if (isset($concat) && !empty($concat)) {
					if ($concat == 'und') {
						if ($value !== Null) {
							$secondparameter = $v['joinParams']['secondparameter'];
							if (isset($v['joinParams']['zeitraum']) && $v['joinParams']['zeitraum'] === true) {
								$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter . ' AND ' . $filter . ' !=  0 OR ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $operator . ' :' . $parameter . ' AND ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' !=  0');
							}
							else {
								$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter);
								if (isset($v['joinParams']['secondparameter']) && !empty($v['joinParams']['secondparameter'])) {
									$secondparameter = $v['joinParams']['secondparameter'];
									$queryBuilder->andWhere($secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $secondparameter['operator'] . ' :' . $secondparameter['value_alias'] );
									$queryBuilder->setParameter($secondparameter['value_alias'], $secondparameter['value']);
								}
							}
						}
						else {
							$queryBuilder->andWhere($filter . ' ' . $operator);
						}
					}
					elseif ($concat == 'oder') {
						if ($value !== Null) {
							$secondparameter = $v['joinParams']['secondparameter'];
							if (isset($v['joinParams']['zeitraum']) && $v['joinParams']['zeitraum'] === true) {
								$queryBuilder->orWhere($filter . ' ' . $operator . ' :' . $parameter . ' AND ' . $filter . ' !=  0 OR ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $operator . ' :' . $parameter . ' AND ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' !=  0');
							}
							else {
								$queryBuilder->orWhere($filter . ' ' . $operator . ' :' . $parameter);
								if (isset($v['joinParams']['secondparameter']) && !empty($v['joinParams']['secondparameter'])) {
									$secondparameter = $v['joinParams']['secondparameter'];
									$queryBuilder->andWhere($secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $secondparameter['operator'] . ' :' . $secondparameter['value_alias'] );
									$queryBuilder->setParameter($secondparameter['value_alias'], $secondparameter['value']);
								}
							}
						}
						else {
							$queryBuilder->orWhere($filter . ' ' . $operator);
						}
					}
				}
				else {
					if ($value !== Null) {
						if (isset($v['joinParams']['secondparameter']) && !empty($v['joinParams']['secondparameter'])) {
							$secondparameter = $v['joinParams']['secondparameter'];
							if (isset($v['joinParams']['zeitraum']) && $v['joinParams']['zeitraum'] === true) {
								$queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter . ' AND ' . $filter . ' !=  0 OR ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $operator . ' :' . $parameter . ' AND ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' !=  0');
							}
							else {
								$queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter);
								$queryBuilder->andWhere($secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $secondparameter['operator'] . ' :' . $secondparameter['value_alias'] );
								$queryBuilder->setParameter($secondparameter['value_alias'], $secondparameter['value']);
							}
						}
						else {
							$queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter);
						}
					}
					else {
						$queryBuilder->where($filter . ' ' . $operator);
					}
				}

				if ($value !== Null) {
					$queryBuilder->setParameter($parameter, $value);
				}

				if (isset($v['concat']) && !empty($v['concat'])) {
					$concat = $v['concat'];
				}

				if (!empty($v['joinParams']['duplicateJoinCheck']) && is_array($v['joinParams']['duplicateJoinCheck'])) {
					$check = array_merge($check, $v['joinParams']['duplicateJoinCheck']);
				}

				if (isset($check) && is_array($check)) {
					$check = array_unique($check);
				}

				if (isset($parameter) && !empty($parameter)) {
					$parameterArr[] = $parameter;
				}
			}
		}

		return $query->execute();
	}

}
?>
