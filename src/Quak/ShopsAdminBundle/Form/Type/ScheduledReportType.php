<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class for schedule create/edit forms
 */
class ScheduledReportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('address', 'text')
            ->add('regions', 'entity', array(
                'class' => 'Quak\ShopsCoreBundle\Entity\Region',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('save', 'submit');
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'Region';
    }
}