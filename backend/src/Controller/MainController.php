<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', methods:['GET'], name: 'app_main')]
    public function main(): Response
    {
        return $this->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Engal services loaded successfully'
        ], 200);
    }
}
