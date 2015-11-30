<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class BandRepository extends Repository
{
    /**
    * @var array An array of associated entities
    */
    protected $entities = ['nummer' => 'band', 'titel' => 'band', 'kurztitel' => 'band', 'sortierung' => 'band', 'bistum' => 'bistum'];

    /*
     * Searches and returns a limited number of Band entities as per search terms
     * @param integer $offset The select offset
     * @param integer $limit The select limit
     * @param array $orderings The ordering parameters
     * @param array $searchArr An array of search terms
     * @param integer $mode The search mode
     * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
     */
    public function searchCertainNumberOfBand($offset, $limit, $orderings, $searchArr, $mode = 1)
    {
        $query = $this->createQuery();
    /** @var $queryBuilder \Doctrine\ORM\QueryBuilder **/
        $queryBuilder = ObjectAccess::getProperty($query, 'queryBuilder', true);
        $queryBuilder
        ->resetDQLParts()
        ->select('band')
        ->from('\Subugoe\GermaniaSacra\Domain\Model\Band', 'band');
        $operator = 'LIKE';
        $isBandInSearchArray = false;
        if (is_array($searchArr) && count($searchArr) > 0) {
            $i = 1;
            foreach ($searchArr as $k => $v) {
                $entity = $this->entities[$k];
                $parameter = $k;
                $searchStr = trim($v);
                $value = '%' . $searchStr . '%';
                $filter = $entity . '.' . $k;
                if ($k === 'bistum') {
                    $queryBuilder->innerJoin('band.bistum', 'bistum');
                    $isBandInSearchArray = true;
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
        if ($orderings[0] === 'bistum' && !$isBandInSearchArray) {
            $queryBuilder->innerJoin('band.bistum', 'bistum');
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

    /*
     * Returns a limited number of Band entities
     * @param $offset The offset
     * @param $limit The limit
     * @param array $orderings The ordering parameters
     * @return \TYPO3\Flow\Persistence\QueryResultInterface The query result
     */
    public function getCertainNumberOfBand($offset, $limit, $orderings)
    {
        $query = $this->createQuery();
        return $query->matching($query->logicalNot($query->like('nummer', 'keine Angabe')))
                ->setOrderings($orderings)
                ->setOffset($offset)
                ->setLimit($limit)
                ->execute();
    }

    /*
     * Returns the number of Band entities
     * @return integer The query result count
     */
    public function getNumberOfEntries()
    {
        return $this->createQuery()->count();
    }
}
