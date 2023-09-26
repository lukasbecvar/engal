<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\EntityHelper;
use App\Helper\LogHelper;

/*
    Login controller provides user authentication
*/

class LoginController extends AbstractController
{

    private $logHelper;
    private $entityHelper;
    private $entityManager;

    public function __construct(
        LogHelper $logHelper,
        EntityHelper $entityHelper,
        EntityManagerInterface $entityManager
    ){
        $this->logHelper = $logHelper;
        $this->entityHelper = $entityHelper;
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'app_login')]
    public function index(Request $request): Response
    {
        // create user instance
        $user = new User();

        // create register form
        $form = $this->createForm(LoginFormType::class, $user);
        $form->handleRequest($request);

        // check if submited
        if ($form->isSubmitted() && $form->isValid()) {
            die('submit');
        }

        return $this->render('login.html.twig', [
            'errorMSG' => null,
            'loginForm' => $form->createView(),
        ]);
    }
}
