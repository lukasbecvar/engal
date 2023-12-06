<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use App\Manager\MediaManager;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploaderController extends AbstractController
{
    private UserManager $userManager;
    private SecurityUtil $securityUtil;
    private MediaManager $mediaManager;

    public function __construct(
        UserManager $userManager, 
        SecurityUtil $securityUtil,
        MediaManager $mediaManager
    ) {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->mediaManager = $mediaManager;
    }

    #[Route('/media/upload', methods:['POST'], name: 'app_media_upload')]
    public function main(Request $request): Response
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
            ], 200);
        }

        // check if token seted
        if ($token == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: token'
            ], 200);
        }

        // check if gallery seted
        if ($gallery == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: gallery'
            ], 200);
        }
        
        // check if image seted
        if (empty($_FILES['image'])) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: image file'
            ], 200);
        }

        // escape user token
        $token = $this->securityUtil->escapeString($token);

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {
                
            // get uploaded file
            $uploaded_file = $_FILES['image'];

            // upload file & get response
            $result = $this->mediaManager->mediaUpload($token, $gallery, $uploaded_file);

            return $this->json($result, 200);
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token value'
            ], 200);
        }
    }
}
