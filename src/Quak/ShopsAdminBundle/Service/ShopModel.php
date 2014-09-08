<?php
namespace Quak\ShopsAdminBundle\Service;

use Doctrine\ORM\EntityManager;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Entity\ShopReport;

/**
 * Logic for shops management
 */
class ShopModel
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param User $shop
     */
    public function removeShop(User $shop)
    {
        $shop->setCurrentReport(null);
        $this->manager->flush();

        $reports = $shop->getReports();

        if ($reports) {
            foreach ($reports as $report) {
                $this->removeReport($report);
            }
        }

        $this->manager->remove($shop);
        $this->manager->flush();
    }

    /**
     * @param ShopReport $report
     */
    protected function removeReport(ShopReport $report)
    {
        $values = $report->getValues();

        foreach ($values as $value) {
            $this->manager->remove($value);
        }
        $this->manager->flush();

        $this->manager->remove($report);
        $this->manager->flush();
    }
}