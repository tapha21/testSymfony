<?php
namespace App\Controller;

use App\Entity\Classe;
use App\Repository\ClasseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClasseController extends AbstractController
{
    private ClasseRepository $classeRepository;

    public function __construct(ClasseRepository $classeRepository)
    {
        $this->classeRepository = $classeRepository;
    }

    #[Route('/classe', name: 'app_classe')]
    public function index(): Response
    {
        $classes = $this->classeRepository->findAll();
        return $this->render('classe/index.html.twig', [
            'controller_name' => 'ClasseController',
            'classes' => $classes,
        ]);
    }

    #[Route("/class/{id}", name:"class_show")]
    public function show($id): Response
    {
        $class = $this->classeRepository->find($id);
        if (!$class) {
            throw $this->createNotFoundException('Classe non trouvée');
        }
        return $this->render('classe/details.html.twig', [
            'class' => $class,
        ]);
    }

    // Route pour créer une classe
    #[Route('/classe/create', name: 'app_classe_create')]
    public function create(Request $request): Response
    {
        // Création d'un nouvel objet Classe
        $classe = new Classe();

        // Création du formulaire pour saisir le nom de la classe
        $form = $this->createFormBuilder($classe)
            ->add('nom', TextType::class, [
                'label' => 'Nom de la classe'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Créer la classe'])
            ->getForm();

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde de la nouvelle classe en base de données
            $this->classeRepository->save($classe, true);

            // Rediriger vers la liste des classes
            return $this->redirectToRoute('app_classe');
        }

        return $this->render('classe/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
