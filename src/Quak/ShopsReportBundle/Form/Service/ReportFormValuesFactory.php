<?php
namespace Quak\ShopsReportBundle\Form\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Quak\ShopsCoreBundle\Entity\ShopReport;
use Quak\ShopsCoreBundle\Entity\FormField;
use Quak\ShopsCoreBundle\Entity\ShopReportValue;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsCoreBundle\Repository\FormFieldRepository;
use Quak\ShopsCoreBundle\Repository\ShopReportValueRepository;
use Quak\ShopsReportBundle\Form\Type\ReportType;

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
     * @param ShopReport $report
     *
     * @return array
     */
    public function createArrayFromReport(ShopReport $report)
    {
        $this->init();

        $data = array();

        $values = $report->getValues();

        foreach ($this->formFields as $field) {
            $value = $this->matchField($field, $values);

            switch ($field->getType()) {
                case FormField::TYPE_TEXT:
                    $key = ReportType::TEXT_FIELD_NAME . $field->getId();
                    $data[$key] = null;
                    if ($value) {
                        $data[$key] = $value->getValue();
                    }
                    break;

                case FormField::TYPE_NUMBER:
                    $key = ReportType::NUMBER_FIELD_NAME . $field->getId();
                    $data[$key] = null;
                    if ($value) {
                        $data[$key] = (int) $value->getValue();
                    }
                    break;

                case FormField::TYPE_NUMBER_TWIN:
                    $key = ReportType::NUMBER_FIELD_NAME . $field->getId();
                    $data[$key . 'a'] = null;
                    $data[$key . 'b'] = null;
                    $data[$key . 'c'] = null;
                    $data[$key . 'd'] = null;
                    if ($value) {
                        $data[$key . 'a'] = (int) $value->getValue();
                        $data[$key . 'b'] = (int) $value->getTwinValue();
                        $data[$key . 'c'] = (int) $value->getThirdValue();
                        $data[$key . 'd'] = (int) $value->getFourthValue();
                    }
                    break;

                case FormField::TYPE_NO_BB:
                    $key = ReportType::NUMBER_FIELD_NAME . $field->getId();
                    $data[$key . 'a'] = null;
                    $data[$key . 'b'] = null;
                    $data[$key . 'c'] = null;
                    if ($value) {
                        $data[$key . 'a'] = (int) $value->getValue();
                        $data[$key . 'b'] = (int) $value->getTwinValue();
                        $data[$key . 'c'] = (int) $value->getThirdValue();
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
     * @param FormField            $field  field
     * @param PersistentCollection $values values
     *
     * @return ShopReportValue|null
     */
    protected function matchField(FormField $field, PersistentCollection $values)
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
                $key = ReportType::TEXT_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key]);
                break;

            case FormField::TYPE_NUMBER:
                $key = ReportType::NUMBER_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key]);
                break;

            case FormField::TYPE_NUMBER_TWIN:
                $key = ReportType::NUMBER_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key . 'a']);
                $value->setTwinValue($data[$key . 'b']);
                $value->setThirdValue($data[$key . 'c']);
                $value->setFourthValue($data[$key . 'd']);
                break;

            case FormField::TYPE_NO_BB:
                $key = ReportType::NUMBER_FIELD_NAME . $value->getField()->getId();
                $value->setValue($data[$key . 'a']);
                $value->setTwinValue($data[$key . 'b']);
                $value->setThirdValue($data[$key . 'c']);
                break;
        }
    }
}
