<?php

namespace App\Form;

use App\Validator\Constraints\Csv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UploadType.
 */
class UploadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'CSV file',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => 'Default']),
                    new File([
                        'groups' => 'Default',
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'text/plain', // because finfo mimetype guesser could fail, rely on validator instead
                            'text/csv', // officially registered type
                            'application/csv',
                            'text/x-csv',
                            'application/x-csv',
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                        ],
                        'mimeTypesMessage' => 'The mime type of the file is invalid ({{ type }}). Allowed mime types are: {{ types }}',
                    ]),
                    new Csv([
                        'groups' => 'Strict',
                        'columnsCount' => 2,
                        'firstLineAsHeader' => true,
                        'delimiter' => ';',
                    ]),
                ],
            ])
            ->add('upload', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
            'validation_groups' => new GroupSequence(['Default', 'Strict']),
        ]);
    }
}
