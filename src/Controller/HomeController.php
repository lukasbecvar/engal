<?php

namespace App\Controller;

use App\Helper\LogHelper;
use App\Helper\LoginHelper;
use App\Util\EncryptionUtil;
use App\Util\StorageUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Home controller provides operations with homepage
*/

class HomeController extends AbstractController
{

    private $loginHelper;
    private $logHelper;

    public function __construct(LoginHelper $loginHelper, LogHelper $logHelper)
    {
        $this->loginHelper = $loginHelper;
        $this->logHelper = $logHelper;
    }

    #[Route(['/', '/home'], name: 'app_home')]
    public function index(): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {
            // get galleries
            $galleries = StorageUtil::getGalleries($this->loginHelper->getUsername());
            // log action
            $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed list of galleries');
            // return galleries
            return $this->render('gallery-list.html.twig', ['galleries' => $galleries]);
        }
    }
}
