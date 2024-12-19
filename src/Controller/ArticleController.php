<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    // Afficher la vue principale des articles
    #[Route('/article', name: 'app_article_list', methods: ['GET'])]
    public function showArticles(): Response
    {
        return $this->render('article/index.html');
    }

    // API pour lister les articles avec pagination et filtrage par libellé
    #[Route('/api/articles', name: 'api_articles', methods: ['GET'])]
    public function apiList(ArticleRepository $articleRepository, Request $request): JsonResponse
    {
        $libelle = $request->query->get('libelle', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 6;

        $queryBuilder = $articleRepository->createQueryBuilder('a');

        if (!empty($libelle)) {
            $queryBuilder->andWhere('a.libelle LIKE :libelle')
                         ->setParameter('libelle', '%' . $libelle . '%');
        }

        $totalArticles = (clone $queryBuilder)->select('COUNT(a.id)')->getQuery()->getSingleScalarResult();

        $queryBuilder->setFirstResult(($page - 1) * $limit)
                     ->setMaxResults($limit);
        $articles = $queryBuilder->getQuery()->getResult();

        $totalPages = (int) ceil($totalArticles / $limit);

        $data = [
            'articles' => array_map(function (Article $article) {
                return [
                    'id' => $article->getId(),
                    'libelle' => $article->getLibelle(),
                    'reference' => $article->getReference(),
                    'prix' => $article->getPrix(),
                    'quantite' => $article->getQuantite(),
                ];
            }, $articles),
            'current_page' => $page,
            'total_pages' => $totalPages,
        ];

        return new JsonResponse($data);
    }

    // Modifier un article existant
    #[Route('/article/edit/{id}', name: 'app_article_edit', methods: ['POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            return $this->json(['error' => 'Article non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['libelle'])) {
            $article->setLibelle($data['libelle']);
        }
        if (isset($data['reference'])) {
            $article->setReference($data['reference']);
        }
        if (isset($data['prix'])) {
            $article->setPrix((float) $data['prix']);
        }
        if (isset($data['quantite'])) {
            $article->setQuantite((int) $data['quantite']);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Article mis à jour avec succès',
            'article' => [
                'id' => $article->getId(),
                'libelle' => $article->getLibelle(),
                'reference' => $article->getReference(),
                'prix' => $article->getPrix(),
                'quantite' => $article->getQuantite(),
            ],
        ]);
    }

    #[Route('/article/create', name: 'article.create', methods: ['GET'])]
        public function create(): Response
        {
            return $this->render('article/edit.html');
        }


                // Décoder les données JSON envoyées dans la requête
                #[Route('/article/store', name: 'article.store', methods: ['POST'])]
                public function store(Request $request, EntityManagerInterface $em): Response
                {
                    $data = json_decode($request->getContent(), true);

                    if (!isset($data['libelle'], $data['reference'], $data['prix'], $data['quantite'])) {
                        return new JsonResponse(['message' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
                    }

                    // Création de l'article
                    $article = new Article();
                    $article->setLibelle($data['libelle']);
                    $article->setReference($data['reference']);
                    $article->setPrix($data['prix']);
                    $article->setQuantite($data['quantite']);

                    try {
                        $em->persist($article);
                        $em->flush();
                        return new JsonResponse(['message' => 'Article créé avec succès'], Response::HTTP_OK);
                    } catch (\Exception $e) {
                        return new JsonResponse(['message' => 'Erreur lors de la création de l\'article : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }

        }


        

