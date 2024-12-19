<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ClientDTO
{
    #[Assert\NotBlank(message: 'Le nom est requis')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
    public ?string $nom = null;

    #[Assert\NotBlank(message: 'Le numéro de téléphone est requis')]
    #[Assert\Regex(
        pattern: '/^\d{10}$/',
        message: 'Le numéro de téléphone doit contenir exactement 10 chiffres'
    )]
    public ?string $telephone = null;

    #[Assert\NotBlank(message: 'L\'adresse est requise')]
    public ?string $adresse = null;

    #[Assert\NotBlank(message: 'L\'email est requis')]
    #[Assert\Email(message: 'L\'email "{{ value }}" n\'est pas un email valide')]
    public ?string $email = null;

    // Ajoutez d'autres propriétés si nécessaire
}
