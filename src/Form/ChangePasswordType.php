<?php namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('currentPassword', PasswordType::class, [
'label' => 'Mot de passe actuel',
'mapped' => false,
'constraints' => [
new Assert\NotBlank(),
],
])
->add('newPassword', PasswordType::class, [
'label' => 'Nouveau mot de passe',
'constraints' => [
new Assert\NotBlank(),
],
]);
}
}