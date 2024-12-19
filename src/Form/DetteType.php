<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Dette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\IntegerType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', IntegerType::class, [ 
                'attr' => [
                    'class' => 'form-control',
                    'required' => true
                ],
                'label' => 'Montant',
            ])
            ->add('montantVerser', IntegerType::class, [ 
                'attr' => [
                    'class' => 'form-control',
                    'required' => true 
                ],
                'label' => 'Montant Versé',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'nom', 
                'label' => 'Client',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dette::class,
        ]);
    }
}
