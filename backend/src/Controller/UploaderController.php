<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UploaderController
 * @package App\Controller
 */
class UploaderController extends AbstractController
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
     * @var StorageManager $storageManager The storage manager.
     */
    private StorageManager $storageManager;

    /**
     * UploaderController constructor.
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     * @param StorageManager $storageManager The storage manager.
     */
    public function __construct(
        UserManager $userManager, 
        SecurityUtil $securityUtil,
        StorageManager $storageManager
    ) {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->storageManager = $storageManager;
    }

    /**
     * Handles the media upload endpoint for uploading images to galleries.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
    #[Route('/media/upload', methods:['POST'], name: 'media_upload')]
    public function uploader(Request $request): Response
    {
        // get post data
        $token = $request->request->get('token');
        $gallery = $request->request->get('gallery');

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

        // check if gallery seted
        if ($gallery == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: gallery'
            ]);
        }
        
        // check if gallery have minimal length
        if (strlen($gallery) <= 3) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'gallery name minimal length is 4 characters'
            ]);
        }

        // check if gallery reached maximal length
        if (strlen($gallery) >= 30) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'gallery name maximal length is 30 characters'
            ]);
        }

        // check if image seted
        if (empty($_FILES['image'])) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: image file'
            ]);
        }

        // escape data
        $token = $this->securityUtil->escapeString($token);
        $gallery = $this->securityUtil->escapeString($gallery);

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {
                
            // get uploaded file
            $uploaded_file = $_FILES['image'];

            // upload file & get response
            $result = $this->storageManager->mediaUpload($token, $gallery, $uploaded_file);

            // return final response
            return $this->json($result);
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'invalid token value'
            ]);
        }
    }
}
