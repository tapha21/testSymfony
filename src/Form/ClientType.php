<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'required' => false, 
                
            ])
            ->add('nom', TextType::class, [
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom  ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'L email ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'required' => false, 
                'attr' => [
                    'placeholder' => '771234567',
                    // 'pattern' => '^(77|78|75|76)([0-9]{7})$',
                    'class' => 'text-red-600 border border-gray-300 rounded-md p-2', 
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de téléphone ne peut pas être vide.',
                    ]),
                    new Regex(
                        '/^(77|78|75|76)[0-9]{7}$/',
                        'Le numéro de téléphone n\'est pas valide.'
                    ),
                ],
            ])
            ->add('adresse', TextareaType::class, [
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'L adresse ne peut pas être vide.',
                    ]),
                ],
            ])
            // ->add('user', UserType::class, [
            //     'label' => false, 
            //     'required' => false,
               
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
