<?php

namespace App\Controller;

use App\Entity\Dette;
use App\Repository\DetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DetteController extends AbstractController
{
    #[Route('/dette', name: 'app_dette_list', methods: ['GET'])]
    public function showArticles(): Response
    {
        return $this->render('dette/index.html');
    }

    // API pour lister les dettes avec pagination
    #[Route('/api/dettes', name: 'api_dette_list', methods: ['GET'])]
    public function index(DetteRepository $detteRepository, Request $request): JsonResponse
    {      
        $page = $request->query->getInt('page', 1);
        $limit = 6;
        
        // Pagination
        $dettes = $detteRepository->PaginateDette($page, $limit);
        $count = $dettes->count();
        $totalPages = ceil($count / $limit);
        $data = [
            'dettes' => array_map(function (Dette $dette) {
                $etat = $dette->getMontant() - $dette-> getMontantVerser() == 0 ? 'payer' : ' impayer';
                return [
                    'id' => $dette->getId(),
                    'date' => $dette->getCreatAt()->format('Y-m-d'),
                    'montant' => $dette->getMontant() - $dette-> getMontantVerser(),
                    'montant_restant'=> $dette->getMontant() - $dette-> getMontantVerser(),
                    'client' => [
                        'prenom' => $dette->getClient()->getPrenom(),
                        'nom' => $dette->getClient()->getNom(),
                    ],
                    'etat' =>$etat, 
                ];
            }, iterator_to_array($dettes->getIterator())),
            'current_page' => $page,
            'total_pages' => $totalPages,
        ];

        return new JsonResponse($data);
    }

    // API pour filtrer les dettes par statut
    #[Route('/dette/filtre', name: 'dette_filtre')]
    public function filtre(Request $request): JsonResponse
        {
            $statut = $request->query->get('statut'); // récupère le statut du filtre
            $page = $request->query->get('page', 1);
            $limit = 10; // Limiter le nombre de résultats par page

            $queryBuilder = $this->entityManager->getRepository(Dette::class)->createQueryBuilder('d');

            if ($statut) {
                $queryBuilder->andWhere('d.statut = :statut')
                            ->setParameter('statut', $statut);
            }

            $total = $queryBuilder->select('COUNT(d.id)')->getQuery()->getSingleScalarResult(); // total des résultats filtrés
            $dettes = $queryBuilder->setFirstResult(($page - 1) * $limit)
                                ->setMaxResults($limit)
                                ->getQuery()
                                ->getResult();

            $response = [
                'total' => $total,
                'dettes' => $dettes,
            ];

            return $this->json($response);
        }

#[Route('/dette/details', name: 'dette.details', methods: ['GET'])]
    public function detailsdette(): Response
    {
        return $this->render('dette/detailsdette.html');
    }

    // API pour afficher les détails d'une dette
    #[Route('/api/dette/{id}/details', name: 'api_dette_details', methods: ['GET'])]
    public function details(Dette $dette): JsonResponse
    {
        $articles = $dette->getArticles();

        $data = [
            'dette' => [
                'id' => $dette->getId(),
                'montant' => $dette->getMontant(),
                'date' => $dette->getDate()->format('Y-m-d'),
                'statut' => $dette->getStatut(),
                'client' => [
                    'id' => $dette->getClient()->getId(),
                    'nom' => $dette->getClient()->getNom(),
                ],
            ],
            'articles' => array_map(function ($article) {
                return [
                    'id' => $article->getId(),
                    'libelle' => $article->getLibelle(),
                    'prix' => $article->getPrix(),
                    'quantite' => $article->getQuantite(),
                ];
            }, $articles->toArray())
        ];

        return new JsonResponse($data);
    }

    #[Route('/dette/create_dette', name: 'client.create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('dette/create_dette.html');
    }
    // API pour créer une nouvelle dette
    #[Route('/api/dette/store', name: 'api_dette_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dette = new Dette();
        $dette->setMontant($data['montant']);
        $dette->setDate(new \DateTime($data['date']));
        $dette->setStatut($data['statut']);

        // Associer un client
        $client = $entityManager->getRepository(Client::class)->find($data['client_id']);
        if (!$client) {
            return $this->json(['error' => 'Client non trouvé'], 404);
        }
        $dette->setClient($client);

        $entityManager->persist($dette);
        $entityManager->flush();

        return $this->json([
            'message' => 'Dette créée avec succès',
            'dette' => [
                'id' => $dette->getId(),
                'montant' => $dette->getMontant(),
                'date' => $dette->getDate()->format('Y-m-d'),
                'statut' => $dette->getStatut(),
            ]
        ], 201);
    }
}
