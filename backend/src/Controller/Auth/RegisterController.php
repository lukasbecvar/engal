<?php

namespace App\Controller\Auth;

use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class RegisterController
 * @package App\Controller\Auth
 */
class RegisterController extends AbstractController
{
    /**
     * @var SiteUtil $siteUtil The site utility.
     */
    private SiteUtil $siteUtil;

    /**
     * @var UserManager $userManager The user manager.
     */
    private UserManager $userManager;

    /**
     * @var SecurityUtil $securityUtil The security utility.
     */
    private SecurityUtil $securityUtil;

    /**
     * RegisterController constructor.
     * @param SiteUtil $siteUtil The site utility.
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(
        SiteUtil $siteUtil, 
        UserManager $userManager, 
        SecurityUtil $securityUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles user registration.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
    #[Route('/register', methods:['POST'], name: 'auth_register')]
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
                'message' => 'registration is disabled'
            ]);   
        }

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

        // check if re-password posted
        if ($re_password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: re-password'
            ]); 
        }  

        // check if inputs including spaces
        if (strpos($username, ' ') && strpos($password, ' ') && strpos($re_password, ' ')) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'spaces is not allowed in login credentials'
            ]);  
        }

        // check minimal username length
        if (strlen($username) <= 3) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'minimal username length is 4 characters'
            ]);  
        }

        // check maximal username maximal
        if (strlen($username) >= 31) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'maximal username length is 30 characters'
            ]);  
        }

        // check minimal password length
        if (strlen($password) <= 7) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'minimal password length is 8 characters'
            ]);  
        }

        // check maximal password maximal
        if (strlen($password) >= 51) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'maximal password length is 50 characters'
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
                'message' => 'passwords not matching'
            ]);  
        }

        // check if username exist
        if ($this->userManager->getUserRepository(['username' => $username]) != null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'username is already in use'
            ]); 
        }

        // insert new user to database
        $this->userManager->insertNewUser($username, $password);

        // return success response
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'user: '.$username.' registered successfully',
            'token' => $this->userManager->getUserToken($username)
        ]);       
    }
}
