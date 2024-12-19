<?php
// src/Controller/MenuController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu", name="menu")
     */
    #[Route('/menu', name: 'menu', methods: ['GET'])]

    public function index()
    {
        return $this->render('base.html');
    }

  

}
