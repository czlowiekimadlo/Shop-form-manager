<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * Base user data fixture
 */
class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        $region = $this->getReference('regionEngland');

        $encoderFactory = $this->container
            ->get('security.encoder_factory');
        // create Administrator
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setName('Administrator');
        $userAdmin->setRoles(array(User::ROLE_ADMIN));
        $userAdmin->setRegion($region);
        $region->addUser($userAdmin);

        $encoder = $encoderFactory->getEncoder($userAdmin);
        $userAdmin->setPassword(
            $encoder->encodePassword('admin1', $userAdmin->getSalt())
        );
        $manager->persist($userAdmin);

        $this->addReference('administrator', $userAdmin);

        // create Demo shop
        $userShop = new User();
        $userShop->setUsername('demoShop');
        $userShop->setName('Shop Demo');
        $userShop->setRoles(array(User::ROLE_SHOP));
        $userShop->setRegion($region);
        $region->addUser($userShop);

        $encoder = $encoderFactory->getEncoder($userShop);
        $userShop->setPassword(
            $encoder->encodePassword('demo', $userShop->getSalt())
        );
        $manager->persist($userShop);
        $this->addReference('demoShop', $userShop);

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 10;
    }
}