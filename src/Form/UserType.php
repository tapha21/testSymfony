<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'attr' => [
                'class' => 'form-control ', 
            ],
            'required' => false, 
        ])
        ->add('prenom', TextType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false
        ])
        ->add('login', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'required' => false
            ],
        ])
        ->add('password', PasswordType::class, [
            'attr' => [
                'class' => 'form-control',
                'required' => false
            ],
        ])  ;     
        
     

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
