<?php

namespace App\Controller;

use App\Helper\LogHelper;
use App\Helper\LoginHelper;
use App\Helper\StorageHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Home controller provides operations with homepage
*/

class HomeController extends AbstractController
{

    private $logHelper;
    private $loginHelper;
    private $storageHelper;

    public function __construct(
        LogHelper $logHelper,
        LoginHelper $loginHelper,
        StorageHelper $storageHelper
    ) {
        $this->logHelper = $logHelper;
        $this->loginHelper = $loginHelper;
        $this->storageHelper = $storageHelper;
    }

    #[Route(['/', '/home'], name: 'app_home')]
    public function index(): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // get current username
            $username = $this->loginHelper->getUsername();

            // get galleries
            $galleries = $this->storageHelper->getGalleries($username);

            // log action
            $this->logHelper->log('gallery', $username.' viewed list of galleries');
            
            // return galleries
            return $this->render('gallery-list.html.twig', ['galleries' => $galleries]);
        }
    }
}
