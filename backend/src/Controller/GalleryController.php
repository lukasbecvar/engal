<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use App\Manager\StorageManager;
use App\Repository\MediaRepository;
use OpenApi\Attributes\Response;
use Symfony\Bundle\SecurityBundle\Security;
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

    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    /**
     * Retrieves the gallery list for the logged-in user.
     *
     * @param Security $security The security component for user authentication
     * @return JsonResponse The JSON response containing the gallery list
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Gallery list by user')]
    #[Response(response: 500, description: 'Gallery list get error')]
    #[Route('/api/gallery/list', name: 'gallery_list', methods: ['GET'])]
    public function indexGalleryList(Security $security): JsonResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // get gallery list array
        $galleryNamesArray = $this->storageManager->getGalleryListByUserId($userId);

        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'gallery_names' => $galleryNamesArray
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Controller method to retrieve statistics about media and galleries for the logged-in user.
     *
     * @param Security $security The security service for handling user authentication.
     *
     * @return JsonResponse The JSON response containing the statistics data.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Get media and gallery count by logged user')]
    #[Route('/api/gallery/stats', name: 'gallery_stats', methods: ['GET'])]
    public function indexGalleryStats(Security $security, MediaRepository $mediaRepository): JsonResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'stats' => [
                'images_count' => $mediaRepository->countMediaByType($userId),
                'videos_count' => $mediaRepository->countMediaByType($userId, 'video'),
                'galleries_count' => count($mediaRepository->findDistinctGalleryNamesByUserId($userId))
            ]
        ], JsonResponse::HTTP_OK);
    }
}
