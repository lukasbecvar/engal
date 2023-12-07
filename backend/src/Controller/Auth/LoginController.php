<?php

namespace App\Controller\Auth;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    private UserManager $userManager;
    private SecurityUtil $securityUtil;

    public function __construct(UserManager $userManager, SecurityUtil $securityUtil)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    #[Route('/login', methods:['POST'], name: 'user_login')]
    public function login(Request $request): Response
    {
        // get post data
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        
        // check if request is post
        if (!$request->isMethod('POST')) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Post request required'
            ], 200);
        } 
        
        // check if username posted
        if ($username == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: username'
            ], 200); 
        }

        // check if password posted
        if ($password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: password'
            ], 200); 
        }

        // escape post data
        $username = $this->securityUtil->escapeString($username);
        $password = $this->securityUtil->escapeString($password);

        // check if user can login
        if ($this->userManager->canLogin($username, $password)) {
                
            // get user token for return to client
            $token = $this->userManager->getUserToken($username);

            // return success response with token
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Login with username: '.$username.' successfully',
                'token' => $token
            ], 200);

        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Incorrect username or password'
            ], 200);
        }
    }      
}
