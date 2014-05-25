<?php
namespace Quak\ShopsCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
        $result = array();

        $users = $this->findAll();

        foreach ($users as $user) {
            $roles = $user->getRoles();

            foreach ($roles as $role) {
                if (!isset($result[$role])) {
                    $result[$role] = array();
                }

                $result[$role][] = $user;
            }
        }

        return $result;
    }
}