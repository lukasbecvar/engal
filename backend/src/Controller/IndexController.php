<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * Index init action to return a JSON response.
     *
     * @return JsonResponse Returns a JSON response with status, code, and backend version.
     */
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'backend_version' => $_ENV['APP_VERSION']
        ], 200);
    }
}
