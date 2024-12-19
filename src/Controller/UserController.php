<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user_list', methods: ['GET'])]
    public function showUsers(): Response
    {
        return $this->render('user/index.html');
    }
    // API pour lister les utilisateurs avec pagination
    #[Route('/api/users', name: 'api_user_list', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): JsonResponse
{
    $page = $request->query->getInt('page', 1);
    $limit = 6;
    $nom = $request->query->get('nom', '');

    // Ajouter le filtre pour le nom
    $users = $userRepository->PaginateUser($page, $limit, $nom); 
    $count = $users->count();
    $totalPages = ceil($count / $limit);

    // Préparer les données
    $data = [
        'users' => array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getLogin(),
                'roles' => $user->getRoles(),
            ];
        }, iterator_to_array($users->getIterator())),
        'current_page' => $page,
        'total_pages' => $totalPages,
    ];

    return new JsonResponse($data);
}

    // API pour afficher les détails d'un utilisateur
    #[Route('/api/user/{id}', name: 'api_user_details', methods: ['GET'])]
    public function details(User $user): JsonResponse
    {
        $data = [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        return new JsonResponse($data);
    }
    #[Route('/user/forms', name: 'user.forms', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('client/forms.html');
    }


    // API pour créer un nouvel utilisateur
    #[Route('/api/user/store', name: 'api_user_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);

        // Encodage du mot de passe si nécessaire
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ], 201);
    }

    // API pour filtrer les utilisateurs par rôle
    #[Route('/api/user/filtre', name: 'api_user_filtre', methods: ['GET'])]
    public function filtre(UserRepository $userRepository, Request $request): JsonResponse
    {
        $role = $request->query->get('role', 'ROLE_USER');
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $users = $userRepository->findByRole($role, $page, $limit); // Ajouter cette méthode dans le repository
        $count = count($users);
        $totalPages = ceil($count / $limit);

        $data = [
            'users' => array_map(function (User $user) {
                return [
                    'id' => $user->getId(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ];
            }, $users),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'role' => $role,
        ];

        return new JsonResponse($data);
    }
}
