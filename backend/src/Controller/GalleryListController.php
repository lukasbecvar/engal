<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\MediaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GalleryListController extends AbstractController
{
    private UserManager $userManager;
    private MediaManager $mediaManager;

    public function __construct(UserManager $userManager, MediaManager $mediaManager)
    {
        $this->userManager = $userManager;
        $this->mediaManager = $mediaManager;
    }

    #[Route('/gallery/list', methods:['POST'], name: 'app_gallery_list')]
    public function galleryList(Request $request): Response
    {
        // get post data
        $token = $request->request->get('token');

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

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {

            // get username
            $username = $this->userManager->getUsernameByToken($token);

            // get gallery list by username
            $gallery_list = $this->mediaManager->getGalleryListByUsername($username);

            // count gallery list
            $gallery_count = count($gallery_list);

            // return final gallery list
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'User: '.$username.' gallery list',
                'count' => $gallery_count,
                'gallery_list' => $gallery_list
            ]);
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token value'
            ]);
        }
    }
}
