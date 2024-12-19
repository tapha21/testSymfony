<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CoursController extends AbstractController
{
    private CoursRepository $coursRepository;

    public function __construct(CoursRepository $coursRepository)
    {
        $this->coursRepository = $coursRepository;
    }

    #[Route('/cours', name: 'app_cours')]
    public function index(Request $request, CoursRepository $coursRepository): Response
    {
        $niveau = $request->query->get('niveau');

        // Récupérer les cours filtrés par niveau
        if ($niveau) {
            $cours = $coursRepository->findBy(['enum' => $niveau]);
        } else {
            // Si aucun filtre n'est appliqué, afficher tous les cours
            $cours = $coursRepository->findAll();
        }

        return $this->render('cours/index.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/create', name: 'app_cours_create')]
    public function create(Request $request): Response
    {
        $cours = new Cours();
        $form = $this->createFormBuilder($cours)
            ->add('nom', TextType::class, [
                'label' => 'Nom du Cours'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Créer le Cours'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->coursRepository->save($cours, true);
            return $this->redirectToRoute('app_cours');
        }

        return $this->render('cours/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
