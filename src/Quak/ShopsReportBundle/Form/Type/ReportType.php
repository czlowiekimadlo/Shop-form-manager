<?php
namespace Quak\ShopsReportBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Quak\ShopsCoreBundle\Entity\FormField;
use Quak\ShopsCoreBundle\Repository\FormFieldRepository;

/**
 * Class for shop report form
 */
class ReportType extends AbstractType
{
    /**
     * @var FormFieldRepository
     */
    protected $formFieldRepository;

    /**
     * @param FormFieldRepository $formFieldRepository
     */
    public function __construct(FormFieldRepository $formFieldRepository)
    {
        $this->formFieldRepository = $formFieldRepository;
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = $this->formFieldRepository->fetchAllSortedByOrdering();

        foreach ($fields as $field) {
            switch ($field->getType()) {
                case FormField::TYPE_TEXT:
                    $this->buildTextField($builder, $field);
                    break;

                case FormField::TYPE_NUMBER:
                    $this->buildNumberField($builder, $field);
                    break;

                case FormField::TYPE_NUMBER_TWIN:
                    $this->buildSetWithBB($builder, $field);
                    break;

                case FormField::TYPE_NO_BB:
                    $this->buildSetWithoutBB($builder, $field);
                    break;
            }
        }


        $builder->add('save', 'submit');
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param FormField            $field   field entity
     */
    protected function buildTextField(FormBuilderInterface $builder, FormField $field)
    {
        $builder->add(
            $field->getFieldName(),
            'text',
            array(
                'label' => $field->getLabel()
            )
        );
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param FormField            $field   field entity
     */
    protected function buildNumberField(FormBuilderInterface $builder, FormField $field)
    {
        $builder->add(
            $field->getFieldName(),
            'number',
            array(
                'label' => $field->getLabel()
            )
        );
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param FormField            $field   field entity
     */
    protected function buildSetWithBB(FormBuilderInterface $builder, FormField $field)
    {
        $this->buildSetWithoutBB($builder, $field);

        $builder->add(
            $field->getFieldName() . 'd',
            'number',
            array(
                'label' => $field->getLabel()
            )
        );
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param FormField            $field   field entity
     */
    protected function buildSetWithoutBB(FormBuilderInterface $builder, FormField $field)
    {
        $builder->add(
            $field->getFieldName() . 'a',
            'number',
            array(
                'label' => $field->getLabel()
            )
        );

        $builder->add(
            $field->getFieldName() . 'b',
            'number',
            array(
                'label' => $field->getLabel()
            )
        );

        $builder->add(
            $field->getFieldName() . 'c',
            'number',
            array(
                'label' => $field->getLabel()
            )
        );
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'ShopReport';
    }
}