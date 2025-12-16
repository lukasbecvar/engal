<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Response;
use App\Repository\MediaRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UserStatusController
 *
 * Controller handling user information
 *
 * @package App\Controller\User
 */
class UserStatusController extends AbstractController
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Retrieves user status data
     *
     * This endpoint retrieves user status data and returns it in JSON format
     *
     * @param UserManager $userManager The user manager service
     * @param Security $security The security service
     *
     * @return JsonResponse The JSON response containing user status data
     */
    #[Tag(name: "User")]
    #[Response(response: 200, description: 'The user data json')]
    #[Response(response: 401, description: 'The JWT token Invalid message')]
    #[Route('/api/user/status', methods: ['GET'], name: 'api_user_status')]
    public function getUserStatus(MediaRepository $mediaRepository, UserManager $userManager, Security $security): JsonResponse
    {
        // get user data
        $userData = $userManager->getUserData($security);

        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // return user data
        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'user_status' => [
                'username' => $userData->getUsername(),
                'roles' => $userData->getRoles(),
                'register_time' => $userData->getRegisterTime(),
                'last_login_time' => $userData->getLastLoginTime(),
                'ip_address' => $userData->getIpAddress()
            ],
            'stats' => [
                'images_count' => $mediaRepository->countMediaByType($userId),
                'videos_count' => $mediaRepository->countMediaByType($userId, 'video'),
                'galleries_count' => count($mediaRepository->findDistinctGalleryNamesByUserId($userId))
            ]
        ], JsonResponse::HTTP_OK);
    }
}
