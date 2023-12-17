<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploaderController extends AbstractController
{
    private UserManager $userManager;
    private SecurityUtil $securityUtil;
    private StorageManager $storageManager;

    public function __construct(
        UserManager $userManager, 
        SecurityUtil $securityUtil,
        StorageManager $storageManager
    ) {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->storageManager = $storageManager;
    }

    #[Route('/media/upload', methods:['POST'], name: 'app_media_upload')]
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
                'message' => 'Post request required'
            ]);
        }

        // check if token seted
        if ($token == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: token'
            ]);
        }

        // check if gallery seted
        if ($gallery == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: gallery'
            ]);
        }
        
        // check if gallery have minimal length
        if (strlen($gallery) <= 3) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Gallery name minimal length is 4 characters'
            ]);
        }

        // check if gallery reached maximal length
        if (strlen($gallery) >= 30) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Gallery name maximal length is 30 characters'
            ]);
        }

        // check if image seted
        if (empty($_FILES['image'])) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: image file'
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

            return $this->json($result);
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token value'
            ]);
        }
    }
}
