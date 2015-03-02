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
	* @var array An array of associated entities for filtering
	*/
	protected  $entities = array('kloster_id' => 'kloster', 'bearbeitungsstatus' => 'bearbeitungsstatus', 'kloster' => 'kloster', 'ort' => 'ort', 'gnd' => 'url', 'bearbeitungsstand' => 'kloster');

	/**
	* @var array An array of associated entities for ordering
	*/
	protected  $orderByEntities = array('kloster_id' => 'kloster', 'name' => 'bearbeitungsstatus', 'kloster' => 'kloster', 'ort' => 'ort', 'url' => 'url', 'bearbeitungsstand' => 'kloster');

	/*
	 * Searches and returns a limited number of Kloster entities as per search terms
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @param array $orderings The ordering parameters
	 * @param array $searchArr An array of search terms
	 * @param integer $mode The search mode
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function searchCertainNumberOfKloster($offset, $limit, $orderings, $searchArr, $mode = 1) {
		$query = $this->createQuery();
	/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('kloster')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'kloster');
		$operator = 'LIKE';
		$isBearbeitungsstatusInSearchArray = False;
		$isOrtInSearchArray = False;
		$isGNDInSearchArray = False;
		if (is_array($searchArr) && count($searchArr) > 0) {
			$i = 1;
			foreach ($searchArr as $k => $v) {
				$entity = $this->entities[$k];
				$parameter = $k;
				$searchStr = trim($v);
				$value = '%' . $searchStr . '%';
				$filter = $entity . '.' . $k;
				if ($k === 'bearbeitungsstatus') {
					$queryBuilder->leftJoin('kloster.bearbeitungsstatus', 'bearbeitungsstatus');
					$isBearbeitungsstatusInSearchArray = True;
					$filter = 'bearbeitungsstatus.name';
				}
				if ($k === 'ort') {
					$queryBuilder->leftJoin('kloster.klosterstandorts', 'klosterstandort')
								->leftJoin('klosterstandort.ort', 'ort');
					$isOrtInSearchArray = True;
					$filter = 'ort.ort';
				}
				if ($k === 'gnd') {
					$queryBuilder->leftJoin('kloster.klosterHasUrls', 'klosterhasurl')
								->leftJoin('klosterhasurl.url', 'url')
								->leftJoin('url.urltyp', 'urltyp');
					$isGNDInSearchArray = True;
					if ($i === 1) {
						$queryBuilder->where('urltyp.name LIKE :urltyp');
						$queryBuilder->andWhere('url.url LIKE :' . $parameter);
					}
					else {
						$queryBuilder->andWhere('urltyp.name LIKE :urltyp');
						$queryBuilder->andWhere('url.url LIKE :' . $parameter);
					}
					$queryBuilder->setParameter('urltyp', 'GND');
					$queryBuilder->setParameter($parameter, $value);
				}
				else {
					if ($i === 1) {
						$queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter);
						$queryBuilder->setParameter($parameter, $value);
					}
					else {
						$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter);
						$queryBuilder->setParameter($parameter, $value);
					}
				}
				$i++;
			}
		}
		if ($orderings[0] === 'ort' && !$isOrtInSearchArray) {
			$queryBuilder->leftJoin('kloster.klosterstandorts', 'klosterstandort')
						->leftJoin('klosterstandort.ort', 'ort');
		}
		elseif ($orderings[0] === 'bearbeitungsstatus' && !$isBearbeitungsstatusInSearchArray) {
			$queryBuilder->leftJoin('kloster.bearbeitungsstatus', 'bearbeitungsstatus');
		}
		elseif ($orderings[0] === 'gnd' && !$isGNDInSearchArray) {
			$queryBuilder->leftJoin('kloster.klosterHasUrls', 'klosterhasurl')
						->leftJoin('klosterhasurl.url', 'url')
						->leftJoin('url.urltyp', 'urltyp');
		}
		if ($orderings[0] === 'bearbeitungsstatus') $orderings[0] = 'name';
		if ($orderings[0] === 'gnd') $orderings[0] = 'url';
		if ($mode === 1) {
			$sort = $this->orderByEntities[$orderings[0]] . '.' . $orderings[0];
			$order = $orderings[1];
			$queryBuilder->orderBy($sort, $order);
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults($limit);
			return $query->execute();
		}
		else {
			return $query->count();
		}
	}

	/*
	 * Returns a limited number of Kloster entities
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @param array $orderings The ordering parameters
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfKloster($offset, $limit, $orderings) {
		$entity = 'kloster';
		$query = $this->createQuery();
		/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('kloster')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'kloster');
		if ($orderings[0] === 'bearbeitungsstatus') {
			$queryBuilder->leftJoin('kloster.bearbeitungsstatus', 'bearbeitungsstatus');
			$entity = 'bearbeitungsstatus';
			$orderings[0] = 'name';
		}
		if ($orderings[0] === 'ort') {
			$queryBuilder->leftJoin('kloster.klosterstandorts', 'klosterstandort')
						->leftJoin('klosterstandort.ort', 'ort');
			$entity = 'ort';
		}
		if ($orderings[0] === 'gnd') {

			$queryBuilder->leftJoin('kloster.klosterHasUrls', 'klosterhasurl')
						->leftJoin('klosterhasurl.url', 'url')
						->leftJoin('url.urltyp', 'urltyp');
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

	/*
	 * Searches and returns a limited number of Kloster entities as per wild card search terms
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @param string $searchValue The search string
	 * @param integer $mode The search mode
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function findKlosterByWildCard($offset, $limit, $orderings, $searchValue, $mode = 1) {
		if (!empty($searchValue)) {
			$query = $this->createQuery();
		/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
			$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
			$queryBuilder
			->resetDQLParts()
			->select('k')
			->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'k');
			$searchValue = "%" . trim($searchValue) . "%";
			$searchValue = (string)$searchValue;
			$queryBuilder->leftJoin('k.klosterstandorts', 's')
						->leftJoin('s.ort', 'o')
						->leftJoin('k.bearbeitungsstatus', 'b')
						->leftJoin('k.band', 'band')
						->leftJoin('o.bistum', 'bistum')
						->leftJoin('o.land', 'land')
						->leftJoin('k.klosterordens', 'klosterorden')
						->leftJoin('klosterorden.orden', 'orden')
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
						->setParameter('alle', $searchValue)
						->setParameter('ist_in_deutschland', 1);
			switch ($orderings[0]) {
			    case 'bearbeitungsstatus':
				 	$entity = 'b';
				 	$orderings[0] = 'name';
			        break;
			    case 'ort':
				 	$entity = 'o';
			        break;
			    case 'gnd':
				    $queryBuilder->leftJoin('k.klosterHasUrls', 'klosterhasurl')
				 				->leftJoin('klosterhasurl.url', 'url')
				 				->leftJoin('url.urltyp', 'urltyp');
				 	$entity = 'url';
				 	$orderings[0] = 'url';
			        break;
				default:
					$entity = 'k';
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
	}

	/*
	 * Returns the number of Kloster entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->countAll();
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

	public function findKlosterByAdvancedSearch($offset, $limit, $orderings, $searchArr, $mode = 1) {
		$query = $this->createQuery();
	/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('kloster')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Kloster', 'kloster');
		$check = array();
		$parameterArr = array();
		if (is_array($searchArr) && count($searchArr) > 0) {
			$ortFilterExists = False;
			$bearbeitungsstatusFilterExists = False;
			foreach ($searchArr as $k => $v) {
				$searchStr = trim($v['text']);
				$filter = $v['filter'];
				if ($filter === 'ort.ort') $ortFilterExists = True;
				if ($filter === 'bearbeitungsstatus.name') $bearbeitungsstatusFilterExists = True;
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
						$queryBuilder->leftJoin($join[0] . '.' . $join[1], $join['2']);
					}
				}
				if (isset($v['joinParams']['secondjoin']) && is_array($v['joinParams']['secondjoin']) && !in_array($v['joinParams']['duplicateJoinCheck'][1],$check)) {
					foreach ($v['joinParams']['secondjoin'] as $secondjoin) {
						$queryBuilder->leftJoin($secondjoin[0] . '.' . $secondjoin[1], $secondjoin['2']);
					}
				}
				if (isset($v['joinParams']['thirdjoin']) && is_array($v['joinParams']['thirdjoin']) && !in_array($v['joinParams']['duplicateJoinCheck'][2],$check)) {
					foreach ($v['joinParams']['thirdjoin'] as $thirdjoin) {
						$queryBuilder->leftJoin($thirdjoin[0] . '.' . $thirdjoin[1], $thirdjoin['2']);
					}
				}
				if (in_array($parameter, $parameterArr)) {
					$parameter = $parameter . '_' . $k;
				}
				if (isset($concat) && !empty($concat)) {
					if ($concat == 'und') {
						if ($value !== Null) {
							if (isset($v['joinParams']['secondparameter']) && !empty($v['joinParams']['secondparameter'])) {
								$secondparameter = $v['joinParams']['secondparameter'];
							}
							if (isset($v['joinParams']['zeitraum']) && $v['joinParams']['zeitraum'] === true) {
								$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter . ' AND ' . $filter . ' !=  0 OR ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $operator . ' :' . $parameter . ' AND ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' !=  0');
							}
							else {
								$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter);
								if (isset($secondparameter) && !empty($secondparameter)) {
									$queryBuilder->andWhere($secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $secondparameter['operator'] . ' :' . $secondparameter['value_alias'] );
									$queryBuilder->setParameter($secondparameter['value_alias'], $secondparameter['value']);
								}
							}
							unset($secondparameter);
						}
						else {
							$queryBuilder->andWhere($filter . ' ' . $operator);
						}
					}
					elseif ($concat == 'oder') {
						if ($value !== Null) {
							if (isset($v['joinParams']['secondparameter']) && !empty($v['joinParams']['secondparameter'])) {
								$secondparameter = $v['joinParams']['secondparameter'];
							}
							if (isset($v['joinParams']['zeitraum']) && $v['joinParams']['zeitraum'] === true) {
								$queryBuilder->orWhere($filter . ' ' . $operator . ' :' . $parameter . ' AND ' . $filter . ' !=  0 OR ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $operator . ' :' . $parameter . ' AND ' . $secondparameter['entity'] . '.' . $secondparameter['property'] . ' !=  0');
							}
							else {
								$queryBuilder->orWhere($filter . ' ' . $operator . ' :' . $parameter);
								if (isset($secondparameter) && !empty($secondparameter)) {
									$queryBuilder->andWhere($secondparameter['entity'] . '.' . $secondparameter['property'] . ' ' . $secondparameter['operator'] . ' :' . $secondparameter['value_alias'] );
									$queryBuilder->setParameter($secondparameter['value_alias'], $secondparameter['value']);
								}
							}
							unset($secondparameter);
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
							unset($secondparameter);
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
		switch ($orderings[0]) {
		    case 'bearbeitungsstatus':

			    if ($bearbeitungsstatusFilterExists) {
				    $entity = 'bearbeitungsstatus';
				    $orderings[0] = 'name';
			    }
				else {
					$queryBuilder->leftJoin('kloster.bearbeitungsstatus', 'bearbeitungsstatus');
					 $entity = 'bearbeitungsstatus';
					$orderings[0] = 'name';
				}
		        break;
		    case 'ort':
				if ($ortFilterExists)
			 	    $entity = 'ort';
				else
					$queryBuilder->leftJoin('kloster.klosterstandorts', 'klosterstandort')
								->leftJoin('klosterstandort.ort', 'ort');
					$entity = 'ort';
		        break;
		    case 'gnd':
			    $queryBuilder->leftJoin('kloster.klosterHasUrls', 'klosterhasurl')
			 				->leftJoin('klosterhasurl.url', 'url')
			 				->leftJoin('url.urltyp', 'urltyp');
			 	$entity = 'url';
			 	$orderings[0] = 'url';
		        break;
			default:
				$entity = 'kloster';
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

}
?>
