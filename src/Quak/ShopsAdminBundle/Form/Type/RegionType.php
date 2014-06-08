<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class for region create/edit forms
 */
class RegionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('shortName', 'text')
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