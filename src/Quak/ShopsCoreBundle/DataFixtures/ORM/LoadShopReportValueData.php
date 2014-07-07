<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\ShopReportValue;

/**
 * Base value data fixture
 */
class LoadShopReportValueData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $value = new ShopReportValue;
        $report = $this->getReference('demoReport');
        $field = $this->getReference('field');

        $report->addValue($value);
        $value->setReport($report);
        $value->setField($field);
        $value->setValue(10);
        $value->setTwinValue(15);

        $this->addReference('demoReportValue', $value);

        $manager->persist($value);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 40;
    }
}