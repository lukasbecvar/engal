<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ImageUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('images', FileType::class, [
            'label' => 'Images',
            'multiple' => true,
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please add image/s',
                ])
            ],
        ])
        ->add('imageName', null, [
            'label' => 'Image name',
            'required' => false,
        ])
        ->add('galleryName', ChoiceType::class, [
            'label' => 'Galerie',
            'choices' => [
                'Galerie 1' => 'galerie1',
                'Galerie 2' => 'galerie2',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a image gallery',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Your gallery name should be at least {{ limit }} characters',
                    'max' => 50,
                ]),
            ],
        ]);
    }
}
