<?php

namespace App\Controller\AccountSettings;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ChangeProfilePicController
 * @package App\Controller\AccountSettings
 */
class ChangeProfilePicController extends AbstractController
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
     * Handles the profile picture change request.
     *
     * @param Request $request The HTTP request.
     *
     * @return Response The HTTP response.
     */
    #[Route('/account/settings/pic', methods:['POST'], name: 'app_account_settings_profile_pic_change')]
    public function profilePicChange(Request $request): Response
    {
        // get post data
        $token = $request->request->get('token');
        $image = $request->request->get('image');

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
        if ($image == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: image (base64)'
            ]);
        }

        // escape post data
        $token = $this->securityUtil->escapeString($token);
        $image = $this->securityUtil->escapeString($image);

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {
            
            // update profile image
            $this->userManager->updateProfilePic($token, $image);

            // return success message
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'profile image update success'
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
