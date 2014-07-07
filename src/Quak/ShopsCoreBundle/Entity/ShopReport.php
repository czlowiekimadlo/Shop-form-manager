<?php
namespace Quak\ShopsCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Entity\ShopReportValue;

/**
 * ShopReport entity
 *
 * @ORM\Table(name="shops_reports")
 * @ORM\Entity(repositoryClass="Quak\ShopsCoreBundle\Repository\ShopReportRepository")
 */
class ShopReport
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Quak\ShopsCoreBundle\Entity\User", inversedBy="reports")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Quak\ShopsCoreBundle\Entity\ShopReportValue", mappedBy="report")
     */
    private $values;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime('now', new \DateTimeZone('Europe/London'));
        $this->values = new ArrayCollection;
    }

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
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param ShopReportValue $value
     */
    public function addValue(ShopReportValue $value)
    {
        $this->values[] = $value;
    }

    /**
     * @return ArrayCollection
     */
    public function getValues()
    {
        return $this->values;
    }
}