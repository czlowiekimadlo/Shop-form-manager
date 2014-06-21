<?php
namespace Quak\ShopsAdminBundle\Report;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsCoreBundle\Repository\UserRepository;
use Quak\ShopsCoreBundle\Repository\FormFieldRepository;
use Quak\ShopsCoreBundle\Repository\ScheduledReportRepository;
use Quak\ShopsAdminBundle\Report\XMLBuilder;

/**
 * Report generator service
 */
class Reporter
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var XMLBuilder
     */
    protected $builder;

    /**
     * \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $sourceMail;

    /**
     * @var ScheduledReportRepository
     */
    protected $scheduledReportRepository;

    /**
     * @var FormFieldRepository
     */
    protected $formFieldRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor
     *
     * @param EntityManager $manager    entity manager
     * @param XMLBuilder    $builder    xml builder
     * @param \Swift_Mailer $mailer     mailer
     * @param string        $sourceMail source mail
     */
    public function __construct(
        EntityManager $manager,
        XMLBuilder $builder,
        \Swift_Mailer $mailer,
        $sourceMail
    )
    {
        $this->manager = $manager;
        $this->builder = $builder;
        $this->mailer = $mailer;
        $this->sourceMail = $sourceMail;

        $this->scheduledReportRepository = $manager
            ->getRepository(Repository::SCHEDULED_REPORT);
        $this->formFieldRepository = $manager
            ->getRepository(Repository::FORM_FIELD);
        $this->userRepository = $manager->getRepository(Repository::USER);
    }

    /**
     * Send scheduled reports
     */
    public function runSchedule()
    {
        $this->sendReports();
        $this->clearCurrentReports();
    }

    /**
     * Generate and send files
     */
    protected function sendReports()
    {
        $scheduledReports = $this->scheduledReportRepository->findAll();
        $timeTag = $this->getTimeTag();

        foreach ($scheduledReports as $report) {
            $xml = $this->buildReport($report);

            $message = $this->composeMessage($report, $xml, $timeTag);
            $this->mailer->send($message);
        }
    }

    /**
     * Clear current reports
     */
    protected function clearCurrentReports()
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $user->setCurrentReport(null);
        }

        $this->manager->flush();
    }

    /**
     * @param ScheduledReport $report
     *
     * @return string
     */
    protected function buildReport(ScheduledReport $report)
    {
        $this->builder->resetState();

        $regions = $report->getRegions();
        $fields = $this->formFieldRepository->fetchAllSortedByOrdering();

        foreach ($regions as $region) {
            $users = $region->getUsers();
            $header = $this->createHeader($users);

            $primaryValues = array();
            $secondaryValues = array();

            foreach ($fields as $field) {
                $primaryRow = array();
                $secondaryRow = array();
                $primaryRow[] = $field->getShort();
                $secondaryRow[] = $field->getShort();

                foreach ($users as $user) {
                    $primaryRow[] = '-';
                    $secondaryRow[] = '-';

                    $currentReport = $user->getCurrentReport();

                    if ($currentReport) {
                        $values = $currentReport->getValues();

                        foreach ($values as $value) {
                            if ($value->getField()->getId()
                                === $field->getId())
                            {
                                $primaryRow[] = $value->getValue();
                                if ($value->getTwinValue() !== null) {
                                    $secondaryRow[] = $value->getTwinValue();
                                }

                                break;
                            }
                        }
                    }
                }

                $primaryValues[] = $primaryRow;
                $secondaryValues[] = $secondaryRow;
            }

            $this->builder->addRow($header);
            foreach ($primaryValues as $row) {
                $this->builder->addRow($row);
            }
            $this->builder
                ->createWorksheet($region->getShortName() . " - Bought");

            $this->builder->addRow($header);
            foreach ($secondaryValues as $row) {
                $this->builder->addRow($row);
            }
            $this->builder
                ->createWorksheet($region->getShortName() . " - Total");
        }

        return $this->builder->generate();
    }

    /**
     * @param ArrayCollection $users
     *
     * @return array
     */
    protected function createHeader(ArrayCollection $users)
    {
        $header = array();
        $header[] = '';

        foreach ($users as $user) {
            $header[] = $user->getName();
        }

        return $header;
    }

    /**
     * @param ScheduledReport $report  report object
     * @param string          $xml     report xml
     * @param string          $timeTag report time tag
     *
     * @return \Swift_Message
     */
    protected function composeMessage(
        ScheduledReport $report,
        $xml,
        $timeTag
    )
    {
        $attachment = $this->buildAttachment(
            'Report-' . $timeTag . '-' . $report->getName() . '.xml',
            $xml
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $timeTag . '] Report - ' . $report->getName())
            ->setFrom($this->sourceMail)
            ->setTo($report->getAddress())
            ->setBody('')
            ->attach($attachment);
    }

    /**
     * @param string $filename file name
     * @param string $xml      xml content
     *
     * @return \Swift_Attachment
     */
    protected function buildAttachment($filename, $xml)
    {
        return \Swift_Attachment::newInstance()
            ->setFilename($filename)
            ->setContentType('application/xml')
            ->setBody($xml);
    }

    /**
     * @return string
     */
    protected function getTimeTag()
    {
        $date = new \DateTime();

        return $date->format('Y-m-d');
    }
}