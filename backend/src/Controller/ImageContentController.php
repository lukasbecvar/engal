<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageContentController extends AbstractController
{
    private UserManager $userManager;
    private StorageManager $storageManager;

    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    #[Route('/image/content', methods:['POST'], name: 'app_image_content')]
    public function imageList(Request $request): Response
    {
        // get post data
        $token = $request->request->get('token');
        $image = $request->request->get('image');
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
                'message' => 'Required post data: gallery (gallery name)'
            ]);
        }

        // check if image seted
        if ($image == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: image (image name)'
            ]);
        }

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {

            // get username
            $username = $this->userManager->getUsernameByToken($token);

            // check if gallery exist
            if ($this->storageManager->checkIfGalleryExist($username, $gallery)) {

                // check if image exist
                if ($this->storageManager->checkIfImageExist($username, $gallery, $image)) {
                    
                    return $this->json([
                        'status' => 'success',
                        'code' => 200,
                        'content' => $this->storageManager->getImageContent($username, $gallery, $image)
                    ]);
                } else {
                    return $this->json([
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Image: '.$image.' not exist'
                    ]);
                }
            } else {
                return $this->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Gallery: '.$gallery.' not exist'
                ]);
            }
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token value'
            ]);
        }
    }
}
