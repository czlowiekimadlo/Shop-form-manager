<?php
namespace Quak\ShopsCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * ShopReport repository class
 */
class ShopReportRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return ShopReport|null
     */
    public function fetchLastSentReportByUser(User $user)
    {
        $currentReport = $user->getCurrentReport();

        $builder = $this->createQueryBuilder('sr')
            ->select('sr')
            ->orderBy('sr.id', 'DESC')
            ->setMaxResults(1);

        if ($currentReport) {
            $builder->where('sr.id != :current')
                ->setParameter('current', $currentReport->getId());
        }

        $result = $builder->getQuery()->getResult();

        if (isset($result[0])) {
            return $result[0];
        }

        return null;
    }
}