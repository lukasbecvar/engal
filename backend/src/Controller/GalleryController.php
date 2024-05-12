<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use App\Manager\ErrorManager;
use OpenApi\Attributes\Response;
use App\Repository\MediaRepository;
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
    private ErrorManager $errorManager;

    public function __construct(UserManager $userManager, ErrorManager $errorManager)
    {
        $this->userManager = $userManager;
        $this->errorManager = $errorManager;
    }

    /**
     * Retrieves the gallery list for the logged-in user.
     *
     * @param Security $security The security component for user authentication
     * @param MediaRepository $mediaRepository The repository for managing media entities
     * @return JsonResponse The JSON response containing the gallery list
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Gallery list by user')]
    #[Response(response: 500, description: 'Gallery list get error')]
    #[Route('/api/gallery/list', name: 'gallery_list', methods: ['GET'])]
    public function index(Security $security, MediaRepository $mediaRepository): JsonResponse
    {
        $galleryNamesArray = [];

        try {
            // get logged user ID
            $userId = $this->userManager->getUserData($security)->getId();

            // get gallery names
            $galleryNames = $mediaRepository->findDistinctGalleryNamesByUserId($userId);

            // build gallery list array
            foreach ($galleryNames as $name) {
                $galleryNamesArray[] = $name['gallery_name'];
            }
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get gallery list: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'gallery_names' => $galleryNamesArray
        ], JsonResponse::HTTP_OK);
    }
}
