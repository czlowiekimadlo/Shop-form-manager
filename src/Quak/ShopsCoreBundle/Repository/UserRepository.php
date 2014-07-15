<?php
namespace Quak\ShopsCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Quak\ShopsCoreBundle\Entity\User;

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
}