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
    const TEXT_FIELD_NAME = 'textField';
    const NUMBER_FIELD_NAME = 'numberField';

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
                    $this->buildNumberTwinField($builder, $field);
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
            self::TEXT_FIELD_NAME . $field->getId(),
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
            self::NUMBER_FIELD_NAME . $field->getId(),
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
    protected function buildNumberTwinField(FormBuilderInterface $builder, FormField $field)
    {
        $builder->add(
            self::NUMBER_FIELD_NAME . $field->getId() . 'a',
            'number',
            array(
                'label' => $field->getLabel() . ' - Bought'
            )
        );

        $builder->add(
            self::NUMBER_FIELD_NAME . $field->getId() . 'b',
            'number',
            array(
                'label' => $field->getLabel() . ' - Cost'
            )
        );

        $builder->add(
            self::NUMBER_FIELD_NAME . $field->getId() . 'c',
            'number',
            array(
                'label' => $field->getLabel() . ' - Stock'
            )
        );

        $builder->add(
            self::NUMBER_FIELD_NAME . $field->getId() . 'd',
            'number',
            array(
                'label' => $field->getLabel() . ' - B&B'
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