<?php
namespace Quak\ShopsCoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Entity\ShopReport;
use Quak\ShopsCoreBundle\Repository\ShopReportRepository;
use Quak\ShopsCoreBundle\Services\ReportFormValuesFactory;

/**
 * Model for shop reports
 */
class ShopReportModel
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var ShopReportRepository
     */
    protected $shopReportRepository;

    /**
     * @var ReportFormValuesFactory
     */
    protected $reportFormValuesFactory;

    /**
     * @param EntityManager           $manager                 entity manager
     * @param ShopReportRepository    $shopReportRepository    reports repository
     * @param ReportFormValuesFactory $reportFormValuesFactory values factory
     */
    public function __construct(
        EntityManager $manager,
        ShopReportRepository $shopReportRepository,
        ReportFormValuesFactory $reportFormValuesFactory
    )
    {
        $this->manager = $manager;
        $this->shopReportRepository = $shopReportRepository;
        $this->reportFormValuesFactory = $reportFormValuesFactory;
    }

    /**
     * @param User $user
     *
     * @return ShopReport
     */
    public function getCurrentStatusReport(User $user)
    {
        $statusReport = $user->getStatusReport();

        if (!$statusReport) {
            $statusReport = new ShopReport;
            $statusReport->setUser($user);
            $this->manager->persist($statusReport);

            $lastReport = $this->shopReportRepository
                ->fetchLastSentReportByUser($user);

            if ($lastReport) {
                $this->updateStatusReport($statusReport, $lastReport);
            }

            $user->setStatusReport($statusReport);

            $this->manager->flush();
        }

        return $statusReport;
    }

    /**
     * @param ShopReport $statusReport status report
     * @param ShopReport $lastReport   last report
     */
    public function updateStatusReport(
        ShopReport $statusReport,
        ShopReport $lastReport
    )
    {
        $lastValues = $this->reportFormValuesFactory
            ->createArrayFromReport($lastReport);
        $this->reportFormValuesFactory
            ->createValuesFromArray($statusReport, $lastValues);
    }
}