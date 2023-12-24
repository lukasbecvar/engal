<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UserStatusController
 * @package App\Controller
 */
class UserStatusController extends AbstractController
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
     * UserStatusController constructor.
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(UserManager $userManager, SecurityUtil $securityUtil)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles the user status endpoint to check the status of a user based on their token.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
    #[Route('/user/status', methods:['POST'], name: 'get_user_status')]
    public function userStatus(Request $request): Response
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
                
            // return success message
            return $this->json([
                'status' => 'success',
                'code' => 200, 
                'username' => $this->userManager->getUsername($token),
                'role' => $this->userManager->getUserRole($token),
                'profile_pic' => $this->userManager->getProfilePic($token)
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
