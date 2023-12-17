<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageListController extends AbstractController
{
    private UserManager $userManager;
    private StorageManager $storageManager;

    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    #[Route('/images', methods:['POST'], name: 'app_images_list')]
    public function imageList(Request $request): Response
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
                'message' => 'Required post data: gallery (gallery name)'
            ]);
        }

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {

            // get username
            $username = $this->userManager->getUsernameByToken($token);

            // check if gallery exist
            if ($this->storageManager->checkIfGalleryExist($username, $gallery)) {
                
                // get image list
                $image_list = $this->storageManager->getImageListWhereGallery($username, $gallery);

                return $this->json([
                    'status' => 'success',
                    'code' => 200,
                    'images' => $image_list
                ]);
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
