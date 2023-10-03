<?php

namespace App\Form;

use App\Helper\LoginHelper;
use App\Helper\StorageHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImageUploadType extends AbstractType
{
    private $loginHelper;
    private $storageHelper;

    public function __construct(LoginHelper $loginHelper, StorageHelper $storageHelper)
    {
        $this->loginHelper = $loginHelper;
        $this->storageHelper = $storageHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('images', FileType::class, [
            'label' => false,
            'multiple' => true,
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please add image/s',
                ])
            ],
            'attr' => [
                'class' => 'form-control mb-3',
                'placeholder' => 'Image',
                'accept' => 'image/*',
                'image_property' => 'image'
            ]
        ])
        ->add('imageName', null, [
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'form-control form-control-lg mb-3',
                'id' => 'form3Example1cg',
                'placeholder' => 'Image name'
            ]
        ])
        ->add('galleryName', ChoiceType::class, [
            'label' => false,
            'choices' => $this->storageHelper->getGalleryListWithPrefix($this->loginHelper->getUsername()),
            'attr' => [
                'class' => 'form-control form-control-lg mb-3',
                'id' => 'form3Example1cg gallery-selection',
                'placeholder' => 'Gallery name',
                'onchange' => 'show(this)'
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
            ]
        ])
        ->add('newGalleryName', null, [
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'form-control form-control-lg mb-3',
                'placeholder' => 'New Gallery name'
            ]
        ]);
    }
}
