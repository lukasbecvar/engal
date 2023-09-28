<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control form-control-lg mb-3',
                'id' => 'form3Example1cg',
                'placeholder' => 'Username',
            ],
            'mapped' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a username',
                ])
            ],
        ])
        ->add('password', PasswordType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control form-control-lg mb-3',
                'id' => 'form3Example1cg',
                'placeholder' => 'Password',
            ],
            'mapped' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ])
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
