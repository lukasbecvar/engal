<?php

namespace App\Controller;

use App\Helper\ErrorHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
    Error controller is error handler (for web server & redirect use)
*/

class ErrorController extends AbstractController
{

    private $errorHelper;

    public function __construct(ErrorHelper $errorHelper) 
    {
        $this->errorHelper = $errorHelper;
    }

    // handle unknow error if code not used
    #[Route('/error', name: 'app_error')]
    public function unknown(): Response
    {
        $this->errorHelper->handleErrorView('unknown');
    }

    // handle error by code
    #[Route('/error/{code}', methods: ['GET'], name: 'code_error')]
    public function index($code): Response
    {
        // block maintenance handeling (this used only from app logic)
        if ($code == 'maintenance') {
            $this->errorHelper->handleErrorView('unknown');
        } else {
            $this->errorHelper->handleErrorView($code);
        }
    }
}
