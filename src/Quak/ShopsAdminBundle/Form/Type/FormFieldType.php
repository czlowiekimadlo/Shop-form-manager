<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Quak\ShopsCoreBundle\Entity\FormField;

/**
 * Class for field create/edit forms
 */
class FormFieldType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text')
            ->add('short', 'text')
            ->add('colour', 'text')
            ->add('type', 'choice',
                array(
                    'choices' => array(
                        FormField::TYPE_TEXT => 'Text',
                        FormField::TYPE_NUMBER => 'Number',
                        FormField::TYPE_NUMBER_TWIN => 'Set with BB',
                        FormField::TYPE_NO_BB => "Set without BB"
                    ),
                    'multiple'  => false,
                    'expanded' => false
                )
            )
            ->add('ordering', 'number')
            ->add('loadPrevious', 'checkbox', array(
                'required' => false
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