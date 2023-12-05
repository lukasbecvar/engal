<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserStatusController extends AbstractController
{
    private UserManager $userManager;
    private SecurityUtil $securityUtil;

    public function __construct(UserManager $userManager, SecurityUtil $securityUtil)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    #[Route('/user/status', methods:['POST'], name: 'app_user_status')]
    public function main(Request $request): Response
    {
        // get user token
        $token = $request->request->get('token');
        
        // check if request is post
        if (!$request->isMethod('POST')) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Post request required'
            ], 200);
        } else {
            // check if token seted
            if ($token == null) {
                return $this->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Required post data: token'
                ], 200);
            }

            // escape user token
            $token = $this->securityUtil->escapeString($token);

            // check if user found in database
            if ($this->userManager->getUserRepository(['token' => $token]) != null) {
                
                // log logout action
                $this->userManager->logLogout($token);

                // return success message
                return $this->json([
                    'status' => 'success',
                    'code' => 200,
                    'username' => $this->userManager->getUsernameByToken($token)
                ], 200);
            } else {
                return $this->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Invalid token value'
                ], 200);
            }
        }
    }
}
