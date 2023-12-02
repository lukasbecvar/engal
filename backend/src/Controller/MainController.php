<?php

namespace App\Controller;

use App\Manager\ErrorManager;
use App\Manager\LogManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private LogManager $logManager;
    private ErrorManager $errorManager;

    public function __construct(ErrorManager $errorManager, LogManager $logManager)
    {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {

        $this->errorManager->handleError('test', 500);
        //$this->logManager->log('testing log', 'test idk');



        return $this->json([
            'status' => 'ok'
        ]);
    }
}
