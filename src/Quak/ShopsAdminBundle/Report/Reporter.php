<?php
namespace Quak\ShopsAdminBundle\Report;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Entity\Region;
use Quak\ShopsCoreBundle\Entity\FormField;
use Quak\ShopsCoreBundle\Entity\ScheduledReport;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsCoreBundle\Repository\UserRepository;
use Quak\ShopsCoreBundle\Repository\RegionRepository;
use Quak\ShopsCoreBundle\Repository\FormFieldRepository;
use Quak\ShopsCoreBundle\Repository\ScheduledReportRepository;
use Quak\ShopsCoreBundle\Services\ShopReportModel;
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
     * @var ShopReportModel
     */
    protected $shopReportModel;

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
     * @var RegionRepository
     */
    protected $regionRepository;

    /**
     * Constructor
     *
     * @param EntityManager   $manager         entity manager
     * @param ShopReportModel $shopReportModel shop report model
     * @param XMLBuilder      $builder         xml builder
     * @param \Swift_Mailer   $mailer          mailer
     * @param string          $sourceMail      source mail
     */
    public function __construct(
        EntityManager $manager,
        ShopReportModel $shopReportModel,
        XMLBuilder $builder,
        \Swift_Mailer $mailer,
        $sourceMail
    )
    {
        $this->manager = $manager;
        $this->builder = $builder;
        $this->mailer = $mailer;
        $this->sourceMail = $sourceMail;
        $this->shopReportModel = $shopReportModel;

        $this->scheduledReportRepository = $manager
            ->getRepository(Repository::SCHEDULED_REPORT);
        $this->formFieldRepository = $manager
            ->getRepository(Repository::FORM_FIELD);
        $this->userRepository = $manager->getRepository(Repository::USER);
        $this->regionRepository = $manager->getRepository(Repository::REGION);
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
     * @param Region $region
     *
     * @return string
     */
    public function generateCurrentReport(Region $region = null)
    {
        if (!$region) {
            $regions = $this->regionRepository->findAll();
        } else {
            $regions = array($region);
        }

        return $this->buildReport($regions);
    }

    /**
     * Generate and send files
     */
    protected function sendReports()
    {
        $scheduledReports = $this->scheduledReportRepository->findAll();
        $timeTag = $this->getTimeTag();

        foreach ($scheduledReports as $report) {
            $xml = $this->buildReport($report->getRegions());

            $message = $this->composeMessage($report, $xml, $timeTag);
            if (!$this->mailer->send($message, $failures)) {
                throw new Exception("Mail not sent");
            }
        }
    }

    /**
     * Clear current reports
     */
    protected function clearCurrentReports()
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $currentReport = $user->getCurrentReport();
            $statusReport = $this->shopReportModel
                ->getCurrentStatusReport($user);

            if ($currentReport) {
                $this->shopReportModel->updateStatusReport(
                    $statusReport,
                    $currentReport
                );
            }

            $user->setCurrentReport(null);
        }

        $this->manager->flush();
    }

    /**
     * @param PersistentCollection|array $regions
     *
     * @return string
     */
    protected function buildReport($regions)
    {
        $this->builder->resetState();
        $fields = $this->formFieldRepository->fetchAllSortedByOrdering();
        $this->addStyles($fields);

        foreach ($regions as $region) {
            $users = $this->userRepository
                ->fetchSortedByOrdering($region);
            $header = $this->createHeader($users);

            $primaryValues = array();
            $secondaryValues = array();
            $tertiaryValues = array();
            $quadValues = array();
            $combinedValues = array();

            foreach ($fields as $field) {
                $primaryRow = array();
                $secondaryRow = array();
                $tertiaryRow = array();
                $quadRow = array();

                $primaryRow[] = $this->prepareCell($field, $field->getShort());
                $secondaryRow[] = $this->prepareCell($field, $field->getShort());
                $tertiaryRow[] = $this->prepareCell($field, $field->getShort());
                $quadRow[] = $this->prepareCell($field, $field->getShort());

                foreach ($users as $user) {
                    if (!$user->hasRole(User::ROLE_SHOP)) {
                        continue;
                    }

                    $primaryValue = '-';
                    $secondaryValue = '-';
                    $tertiaryValue = '-';
                    $quadValue = '-';

                    $currentReport = $user->getCurrentReport();

                    if ($currentReport) {
                        $values = $currentReport->getValues();

                        foreach ($values as $value) {
                            if ($value->getField()->getId()
                                === $field->getId()) {
                                $primaryValue = $value->getValue();
                                if ($value->getTwinValue() !== null) {
                                    $secondaryValue = $value->getTwinValue();
                                }
                                if ($value->getThirdValue() !== null) {
                                    $tertiaryValue = $value->getThirdValue();
                                }
                                if ($value->getFourthValue() !== null) {
                                    $quadValue = $value->getFourthValue();
                                }

                                break;
                            }
                        }
                    }

                    $primaryRow[] = $this->prepareCell($field, $primaryValue);
                    $secondaryRow[] = $this->prepareCell($field, $secondaryValue);
                    $tertiaryRow[] = $this->prepareCell($field, $tertiaryValue);
                    $quadRow[] = $this->prepareCell($field, $quadValue);
                }

                $primaryValues[] = $primaryRow;
                $secondaryValues[] = $secondaryRow;
                $tertiaryValues[] = $tertiaryRow;
                $quadValues[] = $quadRow;

                switch ($field->getType()) {
                    case FormField::TYPE_TEXT:
                    case FormField::TYPE_NUMBER:
                        $combinedValues[] = $primaryRow;
                        break;

                    case FormField::TYPE_NO_BB:
                        $primaryRow[0] = $this->prepareCell($field, $field->getShort() . " - Bought");
                        $secondaryRow[0] = $this->prepareCell($field, $field->getShort() . " - Cost");
                        $tertiaryRow[0] = $this->prepareCell($field, $field->getShort() . " - Stock");
                        $combinedValues[] = $primaryRow;
                        $combinedValues[] = $secondaryRow;
                        $combinedValues[] = $tertiaryRow;
                        break;

                    case FormField::TYPE_NUMBER_TWIN:
                        $primaryRow[0] = $this->prepareCell($field, $field->getShort() . " - Bought");
                        $secondaryRow[0] = $this->prepareCell($field, $field->getShort() . " - Cost");
                        $tertiaryRow[0] = $this->prepareCell($field, $field->getShort() . " - Stock");
                        $quadRow[0] = $this->prepareCell($field, $field->getShort() . " - BB");
                        $combinedValues[] = $primaryRow;
                        $combinedValues[] = $secondaryRow;
                        $combinedValues[] = $tertiaryRow;
                        $combinedValues[] = $quadRow;
                        break;
                }

            }

            $this->buildWorksheet(
                $combinedValues, $header, $region->getShortName()
            );

            $this->buildWorksheet(
                $primaryValues, $header, $region->getShortName() . " - Bought"
            );

            $this->buildWorksheet(
                $secondaryValues, $header, $region->getShortName() . " - Cost"
            );

            $this->buildWorksheet(
                $tertiaryValues, $header, $region->getShortName() . " - Stock"
            );

            $this->buildWorksheet(
                $quadValues, $header, $region->getShortName() . " - BB"
            );
        }

        return $this->builder->generate();
    }

    /**
     * @param array $fields
     */
    protected function addStyles(array $fields)
    {
        foreach ($fields as $field) {
            $colour = $field->getColour();

            if (!empty($colour)) {
                $parameters = array(
                    'bgcolor' => $colour
                );

                $this->builder->addStyle(
                    'cellStyle' . $field->getId(),
                    $parameters
                );
            }
        }
    }

    /**
     * @param FormField $field Field entity
     * @param mixed     $value cell value
     *
     * @return mixed
     */
    protected function prepareCell(FormField $field, $value)
    {
        $colour = $field->getColour();

        if (empty($colour)) {
            return $value;
        }

        return array(
            'data' => $value,
            'attach_style' => 'cellStyle' . $field->getId()
        );
    }

    /**
     * @param array  $rows   rows
     * @param array  $header header row
     * @param string $name   worksheet name
     */
    protected function buildWorksheet(array $rows, array $header, $name)
    {
        $this->builder->addRow($header);
        foreach ($rows as $row) {
            $this->builder->addRow($row);
        }
        $this->builder
            ->createWorksheet($name);
    }

    /**
     * @param mixed $users
     *
     * @return array
     */
    protected function createHeader($users)
    {
        $header = array();
        $header[] = '';

        foreach ($users as $user) {
            if (!$user->hasRole(User::ROLE_SHOP)) {
                continue;
            }

            $header[] = $user->getShortName();
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
            ->setFrom(array($this->sourceMail => "Automatic Reporter"))
            ->setTo($report->getAddress())
            ->setBody('Report for ' . $timeTag)
            ->attach($attachment);

        return $message;
    }

    /**
     * @param string $filename file name
     * @param string $xml      xml content
     *
     * @return \Swift_Attachment
     */
    protected function buildAttachment($filename, $xml)
    {
        file_put_contents('/tmp/report.xml', $xml);

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