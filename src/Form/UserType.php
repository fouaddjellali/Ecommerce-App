<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordConfirmType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user']; // Accéder à l'utilisateur actuel dans les options
        
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom de famille',
                'constraints' => [
                    new NotBlank(),
                ]
            ]);

        // Ajouter le champ des rôles seulement si l'utilisateur est administrateur
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Banni' => 'ROLE_BANNED',
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ]);
        } else {
            // Si l'utilisateur n'est pas administrateur, ne pas afficher ce champ
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                ],
                'expanded' => true,
                'multiple' => true,
                'disabled' => true, // Désactive le champ pour que l'utilisateur ne puisse pas modifier son rôle
                'data' => ['ROLE_USER'], // Définit le rôle comme 'USER'
            ]);
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null,  // Option pour passer l'utilisateur courant au formulaire
        ]);
    }
}
