<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RegisterFormType extends AbstractType
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
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Your username should be at least {{ limit }} characters',
                        'max' => 50,
                    ]),
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
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 80,
                    ]),
                ],
            ])
            ->add('re-password', PasswordType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'type' => 'password',
                    'class' => 'form-control form-control-lg',
                    'id' => 'form3Example1cg',
                    'placeholder' => 'Password again',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password "again"',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password "again" should be at least {{ limit }} characters',
                        'max' => 80,
                    ]),
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
