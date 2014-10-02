<?php
namespace Quak\ShopsAdminBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * Class for user create/edit forms
 */
class UserType extends AbstractType
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     *
     * @return null
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentUser = $this->user;

        $regionOptions = array(
            'class' => 'Quak\ShopsCoreBundle\Entity\Region',
            'multiple' => false,
            'expanded' => false
        );
        $roleChoices = array(
            User::ROLE_SHOP  => "Shop",
            User::ROLE_ADMIN => "Administrator",
            User::ROLE_REGION_ADMIN => "Region Administrator"
        );
        if ($this->user->hasRole(User::ROLE_REGION_ADMIN)) {
            $regionOptions['query_builder'] = function(EntityRepository $er)
                use ($currentUser) {
                return $er->createQueryBuilder('r')
                    ->where('r.id = :id')
                    ->setParameter('id', $currentUser->getRegion()->getId());
            };
            $roleChoices = array(User::ROLE_SHOP  => "Shop");
        }

        $builder
            ->add('username', 'text')
            ->add('password', 'password',
                array(
                    'required' => false
                )
            )
            ->add('name', 'text')
            ->add('shortname', 'text')
            ->add('roles', 'choice',
                array(
                    'choices' => $roleChoices,
                    'multiple'  => true
                )
            )
            ->add('region', 'entity', $regionOptions)
            ->add('ordering', 'number')
            ->add('save', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Quak\ShopsCoreBundle\Entity\User',
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'User';
    }
}