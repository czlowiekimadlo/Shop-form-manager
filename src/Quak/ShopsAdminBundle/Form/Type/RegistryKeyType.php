<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class for registry create/edit forms
 */
class RegistryKeyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', 'textarea')
            ->add('name', 'hidden')
            ->add('save', 'submit');
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'RegistryKey';
    }
}