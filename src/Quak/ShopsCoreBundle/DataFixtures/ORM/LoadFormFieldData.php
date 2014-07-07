<?php
namespace Quak\ShopsCoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Quak\ShopsCoreBundle\Entity\FormField;

/**
 * Base form fields data fixture
 */
class LoadFormFieldData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $field = new FormField;

        $field->setLabel('Something');
        $field->setShort('1p');
        $field->setType(FormField::TYPE_NUMBER_TWIN);
        $field->setOrdering(1);

        $this->addReference('field', $field);

        $manager->persist($field);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }
}