<?php

namespace App\Controller\Auth;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LoginController
 * @package App\Controller\Auth
 */
class LoginController extends AbstractController
{
    /**
     * @var UserManager $userManager The user manager.
     */
    private UserManager $userManager;

    /**
     * @var SecurityUtil $securityUtil The security utility.
     */
    private SecurityUtil $securityUtil;

    /**
     * LoginController constructor.
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(UserManager $userManager, SecurityUtil $securityUtil)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles user login.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
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
                'message' => 'post request required'
            ]);
        } 
        
        // check if username posted
        if ($username == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: username'
            ]); 
        }

        // check if password posted
        if ($password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: password'
            ]); 
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
                'message' => 'login with username: '.$username.' successfully',
                'token' => $token
            ]);

        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'incorrect username or password'
            ]);
        }
    }      
}
