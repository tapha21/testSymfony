<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SessionController extends AbstractController
{
    private SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    #[Route('/session', name: 'app_session')]
    public function index(): Response
    {
        $sessions = $this->sessionRepository->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/session/create', name: 'app_session_create')]
    public function create(Request $request): Response
    {
        $session = new Session();

        $form = $this->createFormBuilder($session)
            ->add('nom', TextType::class, ['label' => 'Nom de la session'])
            ->add('submit', SubmitType::class, ['label' => 'Créer la session'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->sessionRepository->save($session, true);
            return $this->redirectToRoute('app_session');
        }

        return $this->render('session/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
