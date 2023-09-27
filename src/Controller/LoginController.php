<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\EntityHelper;
use App\Helper\HashHelper;
use App\Helper\LoginHelper;
use App\Util\EscapeUtil;

/*
    Login controller provides user authentication
*/

class LoginController extends AbstractController
{

    private $loginHelper;
    private $entityHelper;
    private $hashHelper;

    public function __construct(
        LoginHelper $loginHelper,
        EntityHelper $entityHelper,
        HashHelper $hashHelper
    ){
        $this->loginHelper = $loginHelper;
        $this->entityHelper = $entityHelper;
        $this->hashHelper = $hashHelper;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        // create user instance
        $user = new User();

        // create register form
        $form = $this->createForm(LoginFormType::class, $user);
        $form->handleRequest($request);

        // get form data
        $username = $form->get('username')->getData();
        $password = $form->get('password')->getData();

        // escape values (XSS protection)
        $username = EscapeUtil::special_chars_strip($username);
        $password = EscapeUtil::special_chars_strip($password);

        // check if submited
        if ($form->isSubmitted() && $form->isValid()) {

            // init user entity
            $userEntity = new User;

            // check if user exist
            if ($this->entityHelper->isEntityExist(["username" => $username], $userEntity)) {
                
                // get user data
                $user = $this->entityHelper->getEntityValue(["username" => $username], $userEntity);

                // check if password valid
                if ($this->hashHelper->hash_validate($password , $user->getPassword())) {

                    // set user token (login-token session)
                    $this->loginHelper->login($username, $user->getToken());

                } else {
                    return $this->render('login.html.twig', [
                        'errorMSG' => 'Incorrect username or password.',
                        'loginForm' => $form->createView(),
                    ]);  
                }
            } else {
                return $this->render('login.html.twig', [
                    'errorMSG' => 'Incorrect username or password.',
                    'loginForm' => $form->createView(),
                ]);  
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('login.html.twig', [
            'errorMSG' => null,
            'loginForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        if ($this->loginHelper->isUserLogedin()) {
            $this->loginHelper->logout();
        }

        return $this->redirectToRoute('app_home');
    }
}
