<?php
namespace Quak\ShopsCoreBundle\Tests\Repository;

use Quak\ShopsCoreBundle\Tests\UnitTestCase;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Repository\UserRepository;

/**
 * UserRepository class tests
 */
class UserRepositoryTest extends UnitTestCase
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();

        $this->repository = $this->container->get('repository.user');
    }

    /**
     * @covers Quak\ShopsCoreBundle\Repository\UserRepository::fetchAllGroupedByRole
     */
    public function testFetchAllGroupedByRole()
    {
        $users = $this->repository->fetchAllGroupedByRole();

        $this->assertCount(2, $users);
        $this->assertTrue(isset($users[User::ROLE_ADMIN]));
        $this->assertTrue(isset($users[User::ROLE_SHOP]));
        $this->assertCount(1, $users[User::ROLE_ADMIN]);
        $this->assertCount(1, $users[User::ROLE_SHOP]);
    }
}