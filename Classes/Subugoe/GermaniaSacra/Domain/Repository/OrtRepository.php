<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class OrtRepository extends Repository
{
    /**
    * @var array An array of associated entities
    */
    protected $entities = ['ort' => 'ort', 'gemeinde' => 'ort', 'kreis' => 'ort', 'bistum' => 'bistum', 'wuestung' => 'ort', 'laenge' => 'ort', 'breite' => 'ort'];

    /*
     * Searches and returns a limited number of Ort entities as per search terms
     * @param integer $offset The select offset
     * @param integer $limit The select limit
     * @param array $orderings The ordering parameters
     * @param array $searchArr An array of search terms
     * @param integer $mode The search mode
     * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
     */
    public function searchCertainNumberOfOrt($offset, $limit, $orderings, $searchArr, $mode = 1)
    {
        $query = $this->createQuery();
    /** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
        $queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', true);
        $queryBuilder
        ->resetDQLParts()
        ->select('ort')
        ->from('\Subugoe\GermaniaSacra\Domain\Model\Ort', 'ort');
        $operator = 'LIKE';
        $isOrtInSearchArray = false;
        if (is_array($searchArr) && count($searchArr) > 0) {
            $i = 1;
            foreach ($searchArr as $k => $v) {
                $entity = $this->entities[$k];
                $parameter = $k;
                $searchStr = trim($v);
                $value = '%' . $searchStr . '%';
                $filter = $entity . '.' . $k;
                if ($k === 'bistum') {
                    $queryBuilder->innerJoin('ort.bistum', 'bistum');
                    $isOrtInSearchArray = true;
                }
                if ($i === 1) {
                    $queryBuilder->where($filter . ' ' . $operator . ' :' . $parameter);
                    $queryBuilder->setParameter($parameter, $value);
                } else {
                    $queryBuilder->andWhere($filter . ' ' . $operator . ' :' . $parameter);
                    $queryBuilder->setParameter($parameter, $value);
                }
                $i++;
            }
        }
        if ($orderings[0] === 'bistum' && !$isOrtInSearchArray) {
            $queryBuilder->innerJoin('ort.bistum', 'bistum');
        }
        if ($mode === 1) {
            $sort = $this->entities[$orderings[0]] . '.' . $orderings[0];
            $order = $orderings[1];
            $queryBuilder->orderBy($sort, $order);
            $queryBuilder->setFirstResult($offset);
            $queryBuilder->setMaxResults($limit);
            return $query->execute();
        } else {
            return $query->count();
        }
    }

    /**
     * Finds ort as per entered search string
     *
     * @param string $searchString The entered search string
     * @return \TYPO3\Flow\Persistence\QueryResultInterface The ort
     */
    public function findOrtBySearchString($searchString)
    {
        $searchString = trim($searchString);
        $searchString = '%' . $searchString . '%';
        $query = $this->createQuery();
        /** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
        $queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', true);
        $queryBuilder
        ->resetDQLParts()
        ->select('ort')
        ->from('\Subugoe\GermaniaSacra\Domain\Model\Ort', 'ort')
        ->innerJoin('ort.bistum', 'bistum')
        ->where('ort.ort LIKE :ort')
        ->orderBy('ort.ort', 'ASC');
        $queryBuilder->setParameter('ort', $searchString);
        return $query->execute();
    }

    /**
     * Returns a limited number of Ort entities
     * @param int $offset The select offset
     * @param int $limit The select limit
     * @param array $orderings The ordering parameters
     * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
     */
    public function getCertainNumberOfOrt($offset, $limit, $orderings)
    {
        $query = $this->createQuery()
                ->setOrderings($orderings)
                ->setOffset($offset)
                ->setLimit($limit);
        return $query->execute();
    }

    /*
     * Returns the number of Ort entities
     * @return integer The query result count
     */
    public function getNumberOfEntries()
    {
        return $this->createQuery()->count();
    }
}
