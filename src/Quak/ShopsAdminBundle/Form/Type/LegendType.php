<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class for legend edit forms
 */
class LegendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', 'textarea', array(
                'label' => 'Legend'
            ))
            ->add('name', 'hidden')
            ->add('save', 'submit');
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'Legend';
    }
}