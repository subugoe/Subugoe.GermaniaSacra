<?php
namespace Subugoe\GermaniaSacra\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class LiteraturRepository extends Repository
{
    /**
     * Find by multiple properties
     *
     * @param array $properties Array of properties
     * @param bool $caseSensitive
     * @param bool $cacheResult
     * @return \TYPO3\Flow\Persistence\QueryResultInterface
     */
    public function findByProperties(array $properties, $caseSensitive = true, $cacheResult = false)
    {
        $query = $this->createQuery();
        $constraints = [];
        foreach ($properties as $property => $value) {
            if (!empty($property) && !empty($value)) {
                $constraints[] = $query->equals($property, $value, $caseSensitive);
            }
        }
        return $query->matching($query->logicalAnd($constraints))->execute();
    }
}
