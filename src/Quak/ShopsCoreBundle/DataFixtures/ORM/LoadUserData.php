<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * Base user data fixture
 */
class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setRoles(array(User::ROLE_ADMIN));

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userAdmin);
        $userAdmin->setPassword(
            $encoder->encodePassword('admin1', $userAdmin->getSalt())
        );

        $manager->persist($userAdmin);
        $manager->flush();
    }
}