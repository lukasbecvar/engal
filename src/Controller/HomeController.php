<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
    Home controller provides operations with homepage
*/

class HomeController extends AbstractController
{

    #[Route(['/', '/home'], name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
}
