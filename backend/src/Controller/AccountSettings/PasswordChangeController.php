<?php

namespace App\Controller\AccountSettings;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PasswordChangeController
 * @package App\Controller\AccountSettings
 */
class PasswordChangeController extends AbstractController
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
     * Handles the request to change the user's password.
     *
     * @param Request $request The HTTP request.
     *
     * @return Response JSON response indicating the success or failure of the password change.
     */
    #[Route('/account/settings/password', methods:['POST'], name: 'app_account_settings_password_change')]
    public function passwordChange(Request $request): Response
    {
        // get post data
        $token = $request->request->get('token');
        $password = $request->request->get('password');
        $re_password = $request->request->get('re_password');

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

        // check if password seted
        if ($password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: password'
            ]);
        }

        // check if re_password seted
        if ($re_password == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: re_password'
            ]);
        }

        // check minimal password length
        if (strlen($password) <= 3) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'minimal password length is 4 characters'
            ]);  
        }

        // check maximal password length
        if (strlen($password) >= 51) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'maximal password length is 50 characters'
            ]);  
        }

        // check if passwords matching
        if ($password != $re_password) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'passwords not matching'
            ]);
        }

        // escape post data
        $token = $this->securityUtil->escapeString($token);
        $password = $this->securityUtil->escapeString($password);

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {
            
            // hash password
            $password = $this->securityUtil->genBcryptHash($password, 10);

            // update username
            $this->userManager->updatePassword($token, $password);

            // return success message
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'password update success'
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
