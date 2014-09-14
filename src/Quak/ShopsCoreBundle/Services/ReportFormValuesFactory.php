<?php
namespace Quak\ShopsCoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Quak\ShopsCoreBundle\Entity\ShopReport;
use Quak\ShopsCoreBundle\Entity\FormField;
use Quak\ShopsCoreBundle\Entity\ShopReportValue;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsCoreBundle\Repository\FormFieldRepository;
use Quak\ShopsCoreBundle\Repository\ShopReportValueRepository;

/**
 * Factory for form values
 */
class ReportFormValuesFactory
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var FormFieldRepository
     */
    protected $formFieldRepository;

    /**
     * @var ShopReportRepository
     */
    protected $shopReportRepository;

    /**
     * @var ShopReportValueRepository
     */
    protected $shopReportValueRepository;

    /**
     * @var array
     */
    protected $formFields = array();

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;

        $this->formFieldRepository = $manager
            ->getRepository(Repository::FORM_FIELD);
        $this->shopReportRepository = $manager
            ->getRepository(Repository::SHOP_REPORT);
        $this->shopReportValueRepository = $manager
            ->getRepository(Repository::SHOP_REPORT_VALUE);
    }

    /**
     * @param ShopReport $report report
     * @param array      $data   form data
     */
    public function createValuesFromArray(ShopReport $report, array $data)
    {
        $this->init();

        foreach ($this->formFields as $field) {
            $value = $this->getValue($field, $report);

            $this->fillValue($value, $data);
        }

        $this->manager->flush();
    }

    /**
     * @param ShopReport $report       current report
     * @param ShopReport $statusReport status report
     *
     * @return array
     */
    public function createArrayFromReport(
        ShopReport $report,
        ShopReport $statusReport = null
    )
    {
        $this->init();

        $data = array();

        $values = $report->getValues();

        foreach ($this->formFields as $field) {
            $lastValue = null;
            if ($field->isLoadPrevious()) {
                if ($statusReport) {
                    $lastValues = $statusReport->getValues();

                    $previousField = $field->getPreviousField();
                    if (!$previousField) {
                        $previousField = $field;
                    }

                    $lastValue = $this->matchField(
                        $previousField, $lastValues
                    );
                }
            }

            $value = $this->matchField($field, $values);

            switch ($field->getType()) {
                case FormField::TYPE_TEXT:
                    $key = FormField::TEXT_FIELD_NAME . $field->getId();
                    $data[$key] = null;
                    if ($value) {
                        $data[$key] = $value->getValue();
                    }
                    break;

                case FormField::TYPE_NUMBER:
                    $key = FormField::NUMBER_FIELD_NAME . $field->getId();
                    $data[$key] = null;
                    if ($value) {
                        $data[$key] = $value->getValue();
                    }
                    if (empty($data[$key]) && $lastValue) {
                        $data[$key] = $lastValue->getValue();
                    }
                    break;

                case FormField::TYPE_NUMBER_TWIN:
                    $key = FormField::NUMBER_FIELD_NAME . $field->getId();
                    $data[$key . 'a'] = null;
                    $data[$key . 'b'] = null;
                    $data[$key . 'c'] = null;
                    $data[$key . 'd'] = null;
                    if ($value) {
                        $data[$key . 'a'] = $value->getValue();
                        $data[$key . 'b'] = $value->getTwinValue();
                        $data[$key . 'c'] = $value->getThirdValue();
                        $data[$key . 'd'] = $value->getFourthValue();
                    }
                    if ($data[$key . 'c'] === null && $lastValue) {
                       $data[$key . 'c'] = $lastValue->getThirdValue();
                    }
                    if ($data[$key . 'd'] === null && $lastValue) {
                       $data[$key . 'd'] = $lastValue->getFourthValue();
                    }

                    break;

                case FormField::TYPE_NO_BB:
                    $key = FormField::NUMBER_FIELD_NAME . $field->getId();
                    $data[$key . 'a'] = null;
                    $data[$key . 'b'] = null;
                    $data[$key . 'c'] = null;
                    if ($value) {
                        $data[$key . 'a'] = $value->getValue();
                        $data[$key . 'b'] = $value->getTwinValue();
                        $data[$key . 'c'] = $value->getThirdValue();
                    }
                    if ($data[$key . 'c'] === null && $lastValue) {
                       $data[$key . 'c'] = $lastValue->getThirdValue();
                    }
                    break;
            }
        }

        return $data;
    }

    /**
     * Init service
     */
    protected function init()
    {
        if (empty($this->formFields)) {
            $this->formFields = $this->formFieldRepository
                ->fetchAllSortedByOrdering();
        }
    }

    /**
     * @param FormField  $field  field
     * @param ShopReport $report report
     *
     * @return ShopReportValue
     */
    protected function getValue(FormField $field, ShopReport $report)
    {
        $values = $report->getValues();

        $value = $this->matchField($field, $values);

        if ($value) {
            return $value;
        }

        $value = new ShopReportValue;
        $value->setField($field);
        $value->setReport($report);

        $this->manager->persist($value);

        return $value;
    }

    /**
     * @param FormField $field  field
     * @param mixed     $values values
     *
     * @return ShopReportValue|null
     */
    protected function matchField(FormField $field, $values)
    {
        foreach ($values as $value) {
            if ($field === $value->getField()) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param ShopReportValue $value value entity
     * @param array           $data  form data
     */
    protected function fillValue(ShopReportValue $value, array $data)
    {
        switch ($value->getField()->getType()) {
            case FormField::TYPE_TEXT:
                $key = FormField::TEXT_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key]);
                break;

            case FormField::TYPE_NUMBER:
                $key = FormField::NUMBER_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key]);
                break;

            case FormField::TYPE_NUMBER_TWIN:
                $key = FormField::NUMBER_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key . 'a']);
                $value->setTwinValue($data[$key . 'b']);
                $value->setThirdValue($data[$key . 'c']);
                $value->setFourthValue($data[$key . 'd']);
                break;

            case FormField::TYPE_NO_BB:
                $key = FormField::NUMBER_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key . 'a']);
                $value->setTwinValue($data[$key . 'b']);
                $value->setThirdValue($data[$key . 'c']);
                break;
        }
    }
}
