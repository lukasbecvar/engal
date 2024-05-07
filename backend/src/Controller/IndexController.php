<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use OpenApi\Attributes\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class IndexController
 * 
 * Main app index controller for check api status
 * 
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * Index init action to return a JSON response.
     *
     * @return JsonResponse Returns a JSON response with status, code, and backend version.
     */
    #[Tag(name: "Index")]
    #[Route(['/', '/api'], name: 'index', methods: ['GET'])]
    #[Response(response: 200, description: 'The backend app version and status.')]
    public function index(Security $security): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Engal API is loaded success',
            'backend_version' => $_ENV['APP_VERSION'],
            'security_policy' => [
                'REGISTER_ENABLED' => $_ENV['REGISTER_ENABLED'],
                'MIN_USERNAME_LENGTH' => intval($_ENV['MIN_USERNAME_LENGTH']),
                'MAX_USERNAME_LENGTH' => intval($_ENV['MAX_USERNAME_LENGTH']),
                'MIN_PASSWORD_LENGTH' => intval($_ENV['MIN_PASSWORD_LENGTH']),
                'MAX_PASSWORD_LENGTH' => intval($_ENV['MAX_PASSWORD_LENGTH'])
            ]
        ], 200);
    }
}
