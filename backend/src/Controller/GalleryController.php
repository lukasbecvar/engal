<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Schema;
use App\Manager\StorageManager;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Parameter;
use App\Repository\MediaRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class GalleryController
 *
 * GalleryController: get gallery data info
 *
 * @package App\Controller
 */
class GalleryController extends AbstractController
{
    private UserManager $userManager;
    private StorageManager $storageManager;
    private MediaRepository $mediaRepository;

    public function __construct(UserManager $userManager, StorageManager $storageManager, MediaRepository $mediaRepository)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Retrieves the gallery list for the logged-in user
     *
     * Return gallery list from database by user ID from provided JWT auth token
     *
     * @param Security $security The security component for user authentication
     *
     * @return JsonResponse The JSON response containing the gallery list
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Gallery list by user')]
    #[Response(response: 500, description: 'Gallery list get error')]
    #[Route('/api/gallery/list', methods: ['GET'], name: 'gallery_list')]
    public function getGalleryList(Security $security): JsonResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // get gallery list array
        $galleryNamesArray = $this->storageManager->getGalleryListByUserId($userId);

        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'gallery_list' => $galleryNamesArray
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Retrieves a list of content by gallery name
     *
     * Return gallery data from gallery_name in request parameter but only if logged user have permission to view data
     *
     * @param Security $security The security context
     * @param Request $request The request object
     *
     * @return JsonResponse JSON response containing the gallery data or an error message
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Get list of content by gallery name')]
    #[Response(response: 400, description: 'Unknown gallery_name parameter error')]
    #[Response(response: 404, description: 'Gallery not found in database error')]
    #[Parameter(name: 'gallery_name', in: 'query', schema: new Schema(type: 'string'), description: 'Database gallery name')]
    #[Route('/api/gallery/data', methods: ['GET'], name: 'gallery_data')]
    public function galleryData(Security $security, Request $request): JsonResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // get gallery name from request data
        $galleryName = $request->query->get('gallery_name');

        // check if gallery name given
        if (!isset($galleryName)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'gallery_name parameters is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check if gallery exist
        if (!$this->mediaRepository->isGalleryExists($userId, $galleryName)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'gallery: ' . $galleryName . ' not found in database'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // get gallery content
        $gallery_content = $this->mediaRepository->findAllByProperty($userId, $galleryName);

        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'gallery_data' => $gallery_content
        ], JsonResponse::HTTP_OK);
    }
}
