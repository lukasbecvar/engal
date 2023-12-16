<?php

namespace App\Controller\Auth;

use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    private SiteUtil $siteUtil;
    private UserManager $userManager;
    private SecurityUtil $securityUtil;

    public function __construct(
        SiteUtil $siteUtil, 
        UserManager $userManager, 
        SecurityUtil $securityUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    #[Route('/register', methods:['POST'], name: 'user_register')]
    public function register(Request $request): Response
    {
        // get post data
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $re_password = $request->request->get('re-password');

        // check if registrations is databled
        if ($this->siteUtil->isRegisterEnabled() == false) {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Registration is disabled'
            ]);   
        }

        // check if request is post
        if (!$request->isMethod('POST')) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Post request required'
            ]);
        }
    
        // check if username posted
        if ($username == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: username'
            ]); 
        }

        // check if password posted
        if ($password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: password'
            ]); 
        }

        // check if re-password posted
        if ($re_password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: re-password'
            ]); 
        }  

        // escape post data
        $username = $this->securityUtil->escapeString($username);
        $password = $this->securityUtil->escapeString($password);
        $re_password = $this->securityUtil->escapeString($re_password);

        // check if passwords si matched
        if ($password != $re_password) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Passwords not matching'
            ]);  
        }

        // check if username exist
        if ($this->userManager->getUserRepository(['username' => $username]) != null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Username is already in use'
            ]); 
        }

        // insert new user to database
        $this->userManager->insertNewUser($username, $password);

        // return success response
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'User: '.$username.' registered successfully',
            'token' => $this->userManager->getUserToken($username)
        ]);       
    }
}
