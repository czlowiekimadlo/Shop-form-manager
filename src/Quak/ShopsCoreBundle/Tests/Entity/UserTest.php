<?php
namespace Quak\ShopsCoreBundle\Tests\Entity;

use Quak\ShopsCoreBundle\Tests\UnitTestCase;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * User class tests
 */
class UserTest extends UnitTestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->user = new User;
    }

    /**
     * @test
     */
    public function isUserInterface()
    {
        $this->assertInstanceOf(
            '\Symfony\Component\Security\Core\User\UserInterface',
            $this->user
        );
    }

    /**
     * @test
     */
    public function isSerializable()
    {
        $this->assertInstanceOf('\Serializable', $this->user);
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::setId
     * @covers Quak\ShopsCoreBundle\Entity\User::getId
     */
    public function testSetGetId()
    {
        $id = 10;

        $this->user->setId($id);

        $this->assertEquals($id, $this->user->getId());
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::setUsername
     * @covers Quak\ShopsCoreBundle\Entity\User::getUsername
     */
    public function testSetGetUsername()
    {
        $username = 'test';

        $this->user->setUsername($username);

        $this->assertEquals($username, $this->user->getUsername());
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::getRoles
     */
    public function testGetRoles()
    {
        $expectedRoles = array();

        $this->assertEquals($expectedRoles, $this->user->getRoles());
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::setRoles
     * @covers Quak\ShopsCoreBundle\Entity\User::getRoles
     */
    public function testSetRoles()
    {
        $roles = array(User::ROLE_ADMIN, User::ROLE_SHOP);

        $this->user->setRoles($roles);

        $this->assertEquals($roles, $this->user->getRoles());
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::setPassword
     * @covers Quak\ShopsCoreBundle\Entity\User::getPassword
     */
    public function testSetGetPassword()
    {
        $password = 'password';

        $this->user->setPassword($password);

        $this->assertEquals($password, $this->user->getPassword());
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::setName
     * @covers Quak\ShopsCoreBundle\Entity\User::setName
     */
    public function testSetGetName()
    {
        $name = "Shops user";

        $this->user->setName($name);

        $this->assertEquals($name, $this->user->getName());
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::serialize
     */
    public function testSerialize()
    {
        $id = 10;
        $username = 'test';
        $password = 'password';
        $roles = array(User::ROLE_SHOP);
        $name = "Name";

        $expected = serialize(
            array(
                $id,
                $username,
                $password,
                $roles,
                $name
            )
        );

        $this->user->setId($id);
        $this->user->setUsername($username);
        $this->user->setPassword($password);
        $this->user->setRoles($roles);
        $this->user->setName($name);

        $result = $this->user->serialize();

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers Quak\ShopsCoreBundle\Entity\User::unserialize
     */
    public function testUnserialize()
    {
        $id = 10;
        $username = 'test';
        $password = 'password';
        $roles = array(User::ROLE_SHOP);
        $name = "Name";

        $serialized = serialize(array(
            $id,
            $username,
            $password,
            $roles,
            $name
        ));

        $this->user->unserialize($serialized);

        $this->assertEquals($id, $this->user->getId());
        $this->assertEquals($username, $this->user->getUsername());
        $this->assertEquals($password, $this->user->getPassword());
        $this->assertEquals($roles, $this->user->getRoles());
        $this->assertEquals($name, $this->user->getName());
    }
}