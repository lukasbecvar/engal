<?php

namespace App\Controller\User;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UserStatusController
 * 
 * Controller handling user information.
 * 
 * @package App\Controller\User
 */
class UserStatusController extends AbstractController
{
    /**
     * Retrieves user status data.
     *
     * This endpoint retrieves user status data and returns it in JSON format.
     *
     * @param UserManager $userManager The user manager service.
     * @param Security $security The security service.
     * @return JsonResponse The JSON response containing user status data.
     */
    #[Tag(name: "User")]
    #[Response(response: 200, description: 'The user data json')]
    #[Response(response: 401, description: 'The JWT token Invalid message')]
    #[Route('/api/user/status', methods: ['GET'], name: 'api_user_status')]
    public function userStatus(UserManager $userManager, Security $security): JsonResponse
    {
        // get user data
        $user_data = $userManager->getUserData($security);

        // return user data
        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'username' => $user_data->getUsername(),
            'roles' => $user_data->getRoles(),
            'register_time' => $user_data->getRegisterTime(),
            'last_login_time' => $user_data->getLastLoginTime(),
            'ip_address' => $user_data->getIpAddress()
        ], JsonResponse::HTTP_OK);
    }
}
