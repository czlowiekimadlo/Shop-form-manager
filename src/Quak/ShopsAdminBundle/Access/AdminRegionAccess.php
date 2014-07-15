<?php
namespace Quak\ShopsAdminBundle\Access;

use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsAdminBundle\Access\Filter\RegionFilter;

/**
 * Class for determining region admin access to entities
 */
class AdminRegionAccess
{
    /**
     * @var RegionFilter
     */
    protected $regionFilter;

    /**
     * @param RegionFilter $regionFilter
     */
    public function __construct(RegionFilter $regionFilter)
    {
        $this->regionFilter = $regionFilter;
    }

    /**
     * Filter collection based on user role
     *
     * @param mixed $collection collection of entities
     * @param User  $user       user
     *
     * @return mixed
     */
    public function filterForUser($collection, User $user)
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return $collection;
        }

        return $this->regionFilter->filterByRegion(
            $collection,
            $user->getRegion()
        );
    }
}
