<?php
namespace Quak\ShopsAdminBundle\Access\Filter;

use Quak\ShopsCoreBundle\Entity\Region;

/**
 * Filter objects by region
 */
class RegionFilter
{
    /**
     * Filter collection by region
     *
     * @param mixed  $collection collection of entities
     * @param Region $region     region
     *
     * @return array
     */
    public function filterByRegion($collection, Region $region)
    {
        $filtered = array();

        foreach ($collection as $entity) {
            $entityRegion = $entity->getRegion();

            if ($entityRegion && $entityRegion->getId() === $region->getId()) {
                $filtered[] = $entity;
            }
        }

        return $filtered;
    }
}