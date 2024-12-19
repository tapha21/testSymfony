<?php
namespace App\DTO;

class ClientFilterDTO
{
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
    public ?string $nom = null;
    #[Assert\Regex(
        pattern: '/^\d{10}$/',
        message: 'Le numéro de téléphone doit contenir exactement 10 chiffres'
    )]
    public ?string $telephone = null;

    public function __construct(?string $nom = null, ?string $telephone = null)
    {
        $this->nom = $nom;
        $this->telephone = $telephone;
    }
}
