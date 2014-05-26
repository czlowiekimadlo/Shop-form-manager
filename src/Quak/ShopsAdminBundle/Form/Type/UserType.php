<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * Class for user create/edit forms
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('password', 'password')
            ->add('name', 'text')
            ->add('roles', 'choice',
                array(
                    'choices' => array(
                        User::ROLE_SHOP  => "Shop",
                        User::ROLE_ADMIN => "Administrator",
                    ),
                    'multiple'  => true
                )
            )
            ->add('save', 'submit');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'User';
    }
}