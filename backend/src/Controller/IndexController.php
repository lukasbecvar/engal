<?php

namespace App\Controller;

use OpenApi\Attributes\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    #[Route('/', name: 'index', methods: ['GET'])]
    #[Response(response: 200, description: 'The backend app version and status.')]
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'backend_version' => $_ENV['APP_VERSION'],
            'enabled_registration' => boolval($_ENV['REGISTER_ENABLED'])
        ], 200);
    }
}
