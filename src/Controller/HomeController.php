<?php

namespace App\Controller;

use App\Helper\LoginHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
    Home controller provides operations with homepage
*/

class HomeController extends AbstractController
{

    private $loginHelper;

    public function __construct(LoginHelper $loginHelper)
    {
        $this->loginHelper = $loginHelper;
    }

    #[Route(['/', '/home'], name: 'app_home')]
    public function index(): Response
    {
        // check if user loged in 
        if ($this->loginHelper->isUserLogedin()) {
            return $this->render('gallery-list.html.twig');
        }

        return $this->render('home.html.twig');
    }
}
