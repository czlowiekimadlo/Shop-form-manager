<?php
namespace Quak\ShopsCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Quak\ShopsCoreBundle\Entity\FormField;

/**
 * FormField entity
 *
 * @ORM\Table(name="form_fields")
 * @ORM\Entity(repositoryClass="Quak\ShopsCoreBundle\Repository\FormFieldRepository")
 */
class FormField
{
    const TYPE_TEXT = 0;
    const TYPE_NUMBER = 1;
    const TYPE_NUMBER_TWIN = 2;
    const TYPE_NO_BB = 3;

    const TEXT_FIELD_NAME = 'textField';
    const NUMBER_FIELD_NAME = 'numberField';

    public static $types = array(
        self::TYPE_TEXT => self::TYPE_TEXT,
        self::TYPE_NUMBER => self::TYPE_NUMBER,
        self::TYPE_NUMBER_TWIN => self::TYPE_NUMBER_TWIN,
        self::TYPE_NO_BB => self::TYPE_NO_BB
    );

    protected static $headerType = array(
        self::TYPE_TEXT => false,
        self::TYPE_NUMBER => true,
        self::TYPE_NUMBER_TWIN => false,
        self::TYPE_NO_BB => false,
    );

    protected static $tableType = array(
        self::TYPE_TEXT => false,
        self::TYPE_NUMBER => false,
        self::TYPE_NUMBER_TWIN => true,
        self::TYPE_NO_BB => true,
    );

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $short;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $colour;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ordering;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean")
     */
    private $readonly = false;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean")
     */
    private $loadPrevious = false;

    /**
     * @var FormField
     *
     * @ORM\ManyToOne(targetEntity="Quak\ShopsCoreBundle\Entity\FormField")
     * @ORM\JoinColumn(name="previous_field_id", referencedColumnName="id")
     */
    private $previousField;

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
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->label;
    }

    /**
     * @param string $short
     */
    public function setShort($short)
    {
        $this->short = $short;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $colour
     */
    public function setColour($colour)
    {
        $this->colour = $colour;
    }

    /**
     * @return string
     */
    public function getColour()
    {
        return $this->colour;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param bool $readonly
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
    }

    /**
     * @return bool
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @param bool $previous
     */
    public function setLoadPrevious($previous)
    {
        $this->loadPrevious = $previous;
    }

    /**
     * @return bool
     */
    public function isLoadPrevious()
    {
        return $this->loadPrevious;
    }

    /**
     * @param FormField|null $previousField
     */
    public function setPreviousField(FormField $previousField = null)
    {
        $this->previousField = $previousField;
    }

    /**
     * @return FormField|null
     */
    public function getPreviousField()
    {
        return $this->previousField;
    }

    /**
     * @return bool
     */
    public function isHeader()
    {
        return self::$headerType[$this->type];
    }

    /**
     * @return bool
     */
    public function isTable()
    {
        return self::$tableType[$this->type];
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        switch ($this->type) {
            case FormField::TYPE_TEXT:

                return self::TEXT_FIELD_NAME . $this->id;

            case FormField::TYPE_NUMBER:
            case FormField::TYPE_NUMBER_TWIN:
            case FormField::TYPE_NO_BB:

                return self::NUMBER_FIELD_NAME . $this->id;

            default:

                throw new \Exception('Unsupported type');
        }
    }
}