<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\ScheduledReport;

/**
 * Base scheduled report data fixture
 */
class LoadScheduledReportData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $report = new ScheduledReport;
        $region = $this->getReference('regionEngland');

        $report->setName('Demo');
        $report->setAddress('quak@tlen.pl');
        $report->addRegion($region);
        $region->addScheduledReport($report);

        $this->addReference('scheduledReport', $report);

        $manager->persist($report);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 50;
    }
}