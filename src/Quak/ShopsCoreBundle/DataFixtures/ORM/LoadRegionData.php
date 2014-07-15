<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\Region;

/**
 * Base region data fixture
 */
class LoadRegionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->createRegion('England', 'ENG', $manager);
        $this->createRegion('Scotland', 'SCO', $manager);
        $this->createRegion('Ireland', 'IRE', $manager);

        $manager->flush();
    }

    /**
     * @param string        $name    region name
     * @param string        $short   short region name
     * @param ObjectManager $manager manager
     */
    protected function createRegion($name, $short, ObjectManager $manager)
    {
        $region = new Region;

        $region->setName($name);
        $region->setShortName($short);

        $this->addReference('region' . $name, $region);

        $manager->persist($region);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}