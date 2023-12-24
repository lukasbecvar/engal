<?php

namespace App\Controller\Auth;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LogoutController
 * @package App\Controller\Auth
 */
class LogoutController extends AbstractController
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
     * LogoutController constructor.
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(UserManager $userManager, SecurityUtil $securityUtil)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles user logout.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
    #[Route('/logout', methods:['POST'], name: 'auth_logout')]
    public function logout(Request $request): Response
    {
        // get user token
        $token = $request->request->get('token');
        
        // check if request is post
        if (!$request->isMethod('POST')) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'post request required'
            ]);
        }
        
        // check if token seted
        if ($token == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: token'
            ]);
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
                'message' => 'logout success'
            ]);
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'invalid token value'
            ]);
        }
    }
}
