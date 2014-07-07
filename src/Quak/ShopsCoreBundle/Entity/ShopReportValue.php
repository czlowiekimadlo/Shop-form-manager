<?php
namespace Quak\ShopsCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Quak\ShopsCoreBundle\Entity\ShopReport;
use Quak\ShopsCoreBundle\Entity\FormField;

/**
 * ShopReportValue entity
 *
 * @ORM\Table(name="shops_reports_values")
 * @ORM\Entity(repositoryClass="Quak\ShopsCoreBundle\Repository\ShopReportValueRepository")
 */
class ShopReportValue
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ShopReport
     *
     * @ORM\ManyToOne(targetEntity="Quak\ShopsCoreBundle\Entity\ShopReport", inversedBy="values")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     */
    private $report;

    /**
     * @var FormField
     *
     * @ORM\ManyToOne(targetEntity="Quak\ShopsCoreBundle\Entity\FormField")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    private $field;

    /**
     * @param string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;

    /**
     * @param string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $twinValue;

    /**
     * @param string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $thirdValue;

    /**
     * @param string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fourthValue;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ShopReport $report
     */
    public function setReport(ShopReport $report)
    {
        $this->report = $report;
    }

    /**
     * @return ShopReport
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param FormField $field
     */
    public function setField(FormField $field)
    {
        $this->field = $field;
    }

    /**
     * @return FormField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $twinValue
     */
    public function setTwinValue($twinValue)
    {
        $this->twinValue = $twinValue;
    }

    /**
     * @return string
     */
    public function getTwinValue()
    {
        return $this->twinValue;
    }

    /**
     * @param string $thirdValue
     */
    public function setThirdValue($thirdValue)
    {
        $this->thirdValue = $thirdValue;
    }

    /**
     * @return string
     */
    public function getThirdValue()
    {
        return $this->thirdValue;
    }

    /**
     * @param string $fourthValue
     */
    public function setFourthValue($fourthValue)
    {
        $this->fourthValue = $fourthValue;
    }

    /**
     * @return string
     */
    public function getFourthValue()
    {
        return $this->fourthValue;
    }
}