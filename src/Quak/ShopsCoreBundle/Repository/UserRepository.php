<?php
namespace Quak\ShopsCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Entity\Region;

/**
 * User repository class
 */
class UserRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function fetchAllGroupedByRole()
    {
        $result = array(
            User::ROLE_SHOP => array(),
            User::ROLE_ADMIN => array(),
            User::ROLE_REGION_ADMIN => array()
        );

        $users = $this->findAll();

        foreach ($users as $user) {
            $roles = $user->getRoles();

            foreach ($roles as $role) {
                $result[$role][] = $user;
            }
        }

        return $result;
    }

    /**
     * @param Region $region
     *
     * @return array
     */
    public function fetchSortedByOrdering(Region $region)
    {
        return $this->createQueryBuilder('u')
            ->where('u.region = :region')
            ->setParameter('region', $region)
            ->orderBy('u.ordering', 'ASC')
            ->getQuery()
            ->getResult();
    }
}