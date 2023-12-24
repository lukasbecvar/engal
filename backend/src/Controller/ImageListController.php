<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ImageListController
 * @package App\Controller
 */
class ImageListController extends AbstractController
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
     * ImageListController constructor.
     * @param UserManager $userManager The user manager.
     * @param StorageManager $storageManager The storage manager.
     */
    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    /**
     * Handles the retrieval of a list of images for a specific gallery.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
    #[Route('/images', methods:['POST'], name: 'get_images_list')]
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
                'message' => 'required post data: gallery (gallery name)'
            ]);
        }

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {

            // get username
            $username = $this->userManager->getUsername($token);

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
                    'message' => 'gallery: '.$gallery.' not exist'
                ]);
            }
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'invalid token value'
            ]);
        }
    }
}
