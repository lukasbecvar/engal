<?php

namespace App\Controller;

use App\Entity\User;
use App\Util\EscapeUtil;
use App\Helper\HashHelper;
use App\Helper\LoginHelper;
use App\Form\LoginFormType;
use App\Helper\EntityHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Login controller provides user authentication
*/

class LoginController extends AbstractController
{

    private $hashHelper;
    private $loginHelper;
    private $entityHelper;

    public function __construct(
        HashHelper $hashHelper,
        LoginHelper $loginHelper,
        EntityHelper $entityHelper
    ) {
        $this->hashHelper = $hashHelper;
        $this->loginHelper = $loginHelper;
        $this->entityHelper = $entityHelper;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        // check if user logged in
        if ($this->loginHelper->isUserLogedin()) {
            return $this->redirectToRoute('app_home');   
        }

        // create user instance
        $user = new User();

        // create register form
        $form = $this->createForm(LoginFormType::class, $user);
        $form->handleRequest($request);

        // get form data
        $username = $form->get('username')->getData();
        $password = $form->get('password')->getData();

        // get remember status
        $remember = $form->get('remember')->getData();

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
                    $this->loginHelper->login($username, $user->getToken(), $remember);

                } else { // invalid password error
                    return $this->render('login.html.twig', [
                        'errorMSG' => 'Incorrect username or password.',
                        'loginForm' => $form->createView(),
                    ]);  
                }
            } else { // user not exist error
                return $this->render('login.html.twig', [
                    'errorMSG' => 'Incorrect username or password.',
                    'loginForm' => $form->createView(),
                ]);  
            }

            // redirect to homepage
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
        // logout user (is session found)
        if ($this->loginHelper->isUserLogedin()) {
            $this->loginHelper->logout();
        }

        return $this->redirectToRoute('app_home');
    }
}
