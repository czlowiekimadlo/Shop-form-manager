<?php
namespace Quak\ShopsCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Quak\ShopsCoreBundle\Entity\Region;

/**
 * ScheduledReport entity
 *
 * @ORM\Table(name="reports_schedule")
 * @ORM\Entity(repositoryClass="Quak\ShopsCoreBundle\Repository\ScheduledReportRepository")
 */
class ScheduledReport
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
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $address;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Quak\ShopsCoreBundle\Entity\Region", inversedBy="scheduledReports")
     * @ORM\JoinTable(name="reports_schedule_regions")
     */
    private $regions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->regions = new ArrayCollection;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Region $region
     */
    public function addRegion(Region $region)
    {
        $this->regions[] = $region;
    }

    /**
     * @return ArrayCollection
     */
    public function getRegions()
    {
        return $this->regions;
    }
}