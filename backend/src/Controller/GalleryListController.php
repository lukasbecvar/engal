<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class GalleryListController
 * @package App\Controller
 */
class GalleryListController extends AbstractController
{
    /**
     * @var UserManager $userManager The user manager.
     */
    private UserManager $userManager;

    /**
     * @var StorageManager $storageManager The storage manager.
     */
    private StorageManager $storageManager;

    /**
     * GalleryListController constructor.
     * @param UserManager $userManager The user manager.
     * @param StorageManager $storageManager The storage manager.
     */
    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    /**
     * Handles the retrieval of the gallery list for a user.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
    #[Route('/gallery/list', methods:['POST'], name: 'get_gallery_list')]
    public function galleryList(Request $request): Response
    {
        // get post data
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

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {

            // get username
            $username = $this->userManager->getUsername($token);

            // get gallery list by username
            $gallery_list = $this->storageManager->getGalleryListByUsername($username);

            // count gallery list
            $gallery_count = count($gallery_list);

            // return final gallery list
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'user: '.$username.' gallery list',
                'count' => $gallery_count,
                'gallery_list' => $gallery_list
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
