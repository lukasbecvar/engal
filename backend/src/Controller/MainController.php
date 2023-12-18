<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class MainController
 * @package App\Controller
 */
class MainController extends AbstractController
{
    /**
     * Handles the main endpoint to check the status of Engal services.
     *
     * @return Response The JSON response.
     */
    #[Route('/', methods:['GET'], name: 'app_main')]
    public function main(): Response
    {
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Engal services loaded successfully'
        ]);
    }
}
