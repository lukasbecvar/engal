<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\StorageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ImageContentController
 * @package App\Controller
 */
class ImageContentController extends AbstractController
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
     * ImageContentController constructor.
     * @param UserManager $userManager The user manager.
     * @param StorageManager $storageManager The storage manager.
     */
    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    /**
     * Handles the retrieval of the content of a specific image.
     *
     * @param Request $request The HTTP request.
     * @return Response The JSON response.
     */
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

        // check if image seted
        if ($image == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'required post data: image (image name)'
            ]);
        }

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {

            // get username
            $username = $this->userManager->getUsername($token);

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
                        'message' => 'image: '.$image.' not exist'
                    ]);
                }
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
