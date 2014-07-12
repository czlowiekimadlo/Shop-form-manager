<?php
namespace Quak\ShopsCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Quak\ShopsCoreBundle\Entity\RegistryKey;

/**
 * Registry repository class
 */
class RegistryKeyRepository extends EntityRepository
{
    const KEY_LEGEND = 'legend';

    /**
     * @return RegistryKey
     */
    public function getLegend()
    {
        $legend = $this->findOneByName(self::KEY_LEGEND);

        if (!$legend) {
            $legend = new RegistryKey;
            $legend->setName(self::KEY_LEGEND);
        }

        return $legend;
    }
}