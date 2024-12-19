<?php
namespace App\Form;

use App\DTO\ClientFilterDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class ClientFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Nom'],
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
                    // new Regex(
                    //     '/^(77|78|75|76)[0-9]{7}$/',
                    //     'Le numéro de téléphone n\'est pas valide.'
                    // ),
                ],
            ])
            ->add('filter', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientFilterDTO::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}