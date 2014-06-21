<?php
namespace Quak\ShopsCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FormField repository class
 */
class FormFieldRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function fetchAllSortedByOrdering()
    {
        return $this->createQueryBuilder('ff')
            ->select('ff')
            ->orderBy('ff.ordering')
            ->getQuery()
            ->getResult();
    }
}