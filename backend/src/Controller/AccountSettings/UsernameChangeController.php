<?php

namespace App\Controller\AccountSettings;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UsernameChangeController
 * @package App\Controller\AccountSettings
 */
class UsernameChangeController extends AbstractController
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
     * ChangeProfilePicController constructor.
     *
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(UserManager $userManager, SecurityUtil $securityUtil)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles the request to change the username.
     *
     * @param Request $request The HTTP request.
     *
     * @return Response JSON response indicating the success or failure of the username change.
     */
    #[Route('/account/settings/username', methods:['POST'], name: 'app_account_settings_username_change')]
    public function usernameChange(Request $request): Response
    {
        // get post data
        $token = $request->request->get('token');
        $new_username = $request->request->get('new_username');

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

        // check if image seted
        if ($new_username == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: new_username'
            ]);
        }

        // check minimal username length
        if (strlen($new_username) <= 3) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'minimal username length is 4 characters'
            ]);  
        }

        // check maximal username length
        if (strlen($new_username) >= 51) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'maximal username length is 50 characters'
            ]);  
        }


        // escape post data
        $token = $this->securityUtil->escapeString($token);
        $new_username = $this->securityUtil->escapeString($new_username);

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {
            
            // update username
            $this->userManager->updateUsername($token, $new_username);

            // return success message
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'username updated success'
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
