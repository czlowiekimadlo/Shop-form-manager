<?php
namespace Quak\ShopsCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Quak\ShopsCoreBundle\Entity\Region;
use Quak\ShopsCoreBundle\Entity\ShopReport;

/**
 * User entity
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Quak\ShopsCoreBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_SHOP = 'ROLE_SHOP';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_REGION_ADMIN = 'ROLE_REGION_ADMIN';

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
     * @ORM\Column(type="string", length=25, unique=true, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512, nullable=false)
     */
    private $password;

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    private $roles = array();

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
    private $shortName;

    /**
     * @ORM\ManyToOne(targetEntity="Quak\ShopsCoreBundle\Entity\Region", inversedBy="users")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Quak\ShopsCoreBundle\Entity\ShopReport", mappedBy="user")
     */
    private $reports;

    /**
     * @ORM\OneToOne(targetEntity="Quak\ShopsCoreBundle\Entity\ShopReport")
     * @ORM\JoinColumn(name="current_report_id", referencedColumnName="id")
     */
    private $currentReport;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reports = new ArrayCollection;
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
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param string $role
     */
    public function removeRole($role)
    {
        if (($key = array_search($role, $this->roles)) !== false) {
            unset($this->roles[$key]);
        }
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
     * @param string $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * @return Region|null
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param ShopReport $report
     */
    public function addReport(ShopReport $report)
    {
        $this->reports[] = $report;
    }

    /**
     * @return ArrayCollection
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param ShopReport|null $report
     */
    public function setCurrentReport(ShopReport $report = null)
    {
        $this->currentReport = $report;
    }

    /**
     * @return ShopReport|null
     */
    public function getCurrentReport()
    {
        return $this->currentReport;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
            $this->name
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->roles,
            $this->name
        ) = unserialize($serialized);
    }
}