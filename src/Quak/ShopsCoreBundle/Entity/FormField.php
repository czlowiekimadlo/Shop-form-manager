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

    public static $types = array(
        self::TYPE_TEXT => self::TYPE_TEXT,
        self::TYPE_NUMBER => self::TYPE_NUMBER,
        self::TYPE_NUMBER_TWIN => self::TYPE_NUMBER_TWIN
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
}