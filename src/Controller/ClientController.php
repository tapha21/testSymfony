<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Form\ClientFilterType;
use App\Form\ClientType;
use App\Form\UserType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client_list', methods: ['GET'])]
    public function showClient(): Response
    {
        return $this->render('client/index.html');
    }

    // Affichage des clients avec pagination et filtre
    #[Route('api/client', name: 'api_client', methods: ['GET', 'POST'])]
    public function index(ClientRepository $clientRepository, Request $request, FormFactoryInterface $formFactory, SerializerInterface $serializer): JsonResponse
{
    $form = $formFactory->create(ClientFilterType::class);
    $form->handleRequest($request);

    $page = max(1, $request->query->getInt('page', 1));
    $limit = 6;
    if ($form->isSubmitted() && $form->isValid()) {
        $filters = $form->getData();
        $clients = $clientRepository->filterClients($filters, $page, $limit);
        $totalClients = $clientRepository->countFilteredClients($filters);
    } else {
        $clients = $clientRepository->PaginateClient($page, $limit);
        $totalClients = $clientRepository->count([]);
    }

    $serializedClients = $serializer->serialize($clients, 'json', ['groups' => 'client']);

    // Calculer le nombre de pages
    $totalPages = (int) ceil($totalClients / $limit);

    return new JsonResponse([
        'clients' => json_decode($serializedClients),
        'current_page' => $page,
        'total_pages' => $totalPages,
    ]);
    }

    #[Route('/client/forms', name: 'client.forms', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('client/forms.html');
    }

    // Création d'un client (et éventuellement d'un utilisateur)
    #[Route('/client/store', name: 'client.store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données JSON envoyées par le client
        $data = json_decode($request->getContent(), true);
    
        // Créer un nouveau client
        $client = new Client();
        $user = null;
    
        // Vérification et création du client
        if (isset($data['client'])) {
            $client->setPrenom($data['client']['prenom'] ?? null); 
            $client->setNom($data['client']['nom'] ?? null);
            $client->setEmail($data['client']['email'] ?? null);
            $client->setTelephone($data['client']['telephone'] ?? null);
            $client->setAdresse($data['client']['adresse'] ?? null);
            $client->setCreatedAt(new \DateTimeImmutable()); // Date de création
            $client->setUpdatedAt(new \DateTimeImmutable()); // Date de mise à jour
            $entityManager->persist($client); // Persister le client
        }
    
        // Si des données d'utilisateur sont envoyées, création de l'utilisateur
        if (isset($data['user']) && $data['user'] !== null) {
            $user = new User();
            $user->setUsername($data['user']['username'] ?? null); 
            $user->setPassword(password_hash($data['user']['password'], PASSWORD_BCRYPT)); // Hash du mot de passe
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($user); // Persister l'utilisateur
    
            // Associer l'utilisateur au client
            $client->setUser($user);
        }
    
        // Sauvegarder les changements en base de données
        $entityManager->flush();
    
        // Retourner une réponse JSON avec le message de succès
        return $this->json([
            'message' => 'Client created successfully',
            'client' => $client,
            'user' => $user, // Cela sera null si aucun utilisateur n'est créé
        ], JsonResponse::HTTP_CREATED);
    }
    
    // Suppression d'un client
    #[Route('/client/remove/{id}', name: 'client.remove', methods: ['DELETE'])]
    public function remove(int $id, EntityManagerInterface $entityManager, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($id);

        if (!$client) {
            return $this->json(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($client);
        $entityManager->flush();

        return $this->json(['message' => 'Client removed successfully'], JsonResponse::HTTP_OK);
    }

    // Recherche de clients par téléphone
    #[Route('/clients/search', name: 'clients.search', methods: ['GET'])]
    public function search(Request $request, ClientRepository $clientRepository): JsonResponse
    {
        $telephone = $request->query->get('telephone', '');

        $clients = $clientRepository->searchByTelephone($telephone);

        return $this->json([
            'clients' => $clients,
        ]);
    }
}
