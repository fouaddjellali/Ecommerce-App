<?php namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('amount', MoneyType::class, [
'currency' => 'EUR',
'label' => 'Montant',
])
->add('method', TextType::class, [
'label' => 'Méthode de paiement',
])
->add('date', DateType::class, [
'widget' => 'single_text',
'label' => 'Date de paiement',
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Payment::class,
]);
}
}