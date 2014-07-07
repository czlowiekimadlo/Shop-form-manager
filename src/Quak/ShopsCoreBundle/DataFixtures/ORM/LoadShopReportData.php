<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\ShopReport;

/**
 * Base report data fixture
 */
class LoadShopReportData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $report = new ShopReport;
        $user = $this->getReference('demoShop');

        $report->setUser($user);
        $user->setCurrentReport($report);
        $user->addReport($report);

        $this->addReference('demoReport', $report);

        $manager->persist($report);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 30;
    }
}