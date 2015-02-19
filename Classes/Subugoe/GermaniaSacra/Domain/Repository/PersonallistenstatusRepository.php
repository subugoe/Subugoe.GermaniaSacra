<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class PersonallistenstatusRepository extends Repository {

	/**
	* @var array An array of associated entities
	*/
	protected  $entities = array('name' => 'personallistenstatus');

	/*
	 * Searches and returns a limited number of Personallistenstatus entities as per search terms or the total number of search result
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @param array $orderings The ordering parameters
	 * @param array $searchArr An array of search terms
	 * @param integer $mode The search mode
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function searchCertainNumberOfPersonallistenstatus($offset, $limit, $orderings, $searchArr, $mode) {
		$query = $this->createQuery();
		/** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
		$queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder
		->resetDQLParts()
		->select('personallistenstatus')
		->from('\Subugoe\GermaniaSacra\Domain\Model\Personallistenstatus', 'personallistenstatus');
		$operator = 'LIKE';
		if (is_array($searchArr) && count($searchArr) > 0) {
			$i = 1;
			foreach ($searchArr as $k => $v) {
				$entity = $this->entities[$k];
				$parameter = $k;
				$searchStr = trim($v);
				$value = '%' . $searchStr . '%';
				$filter = $entity . '.' . $k;
				if ($i === 1) {
					$queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter);
					$queryBuilder->setParameter($parameter, $value);
				}
				else {
					$queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter);
					$queryBuilder->setParameter($parameter, $value);
				}
				$i++;
			}
		}
		if ($mode === 1) {
			$sort = $this->entities[$orderings[0]] . '.' . $orderings[0];
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
	 * Returns a limited number of Personallistenstatus entities
	 * @param integer $offset The select offset
	 * @param integer $limit The select limit
	 * @param array $orderings The ordering parameters
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function getCertainNumberOfPersonallistenstatus($offset, $limit, $orderings) {
	    $query = $this->createQuery()
			    ->setOrderings($orderings)
				->setOffset($offset)
				->setLimit($limit);
		return $query->execute();
	}

	/*
	 * Returns the number of Personallistenstatus entities
	 * @return integer The query result count
	 */
	public function getNumberOfEntries() {
		return $this->createQuery()->count();
	}

}
?>