<?php

namespace App\Controller;

use App\Helper\LoginHelper;
use App\Util\EscapeUtil;
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

    public function __construct(LoginHelper $loginHelper)
    {
        $this->loginHelper = $loginHelper;
    }

    #[Route(['/', '/home'], name: 'app_home')]
    public function index(): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        }

        // get gallerys
        $gallerys = StorageUtil::getGallerys($this->loginHelper->getUsername());

        return $this->render('gallery-list.html.twig', ['gallerys' => $gallerys]);
    }

    #[Route(['/', '/gallery'], name: 'app_empty_gallery')]
    public function emptyGallery(): Response
    {
        return $this->redirectToRoute('code_error', [
            'code' => '400'
        ]);
    }

    #[Route(['/', '/gallery/{name}'], name: 'app_gallery')]
    public function gallery($name): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        }

        // escape name
        $name = EscapeUtil::special_chars_strip($name);

        // check if gallery exist
        if (StorageUtil::checkGallery($this->loginHelper->getUsername(), $name)) {
            
            // get gallery images
            $images = StorageUtil::getImagesContent($this->loginHelper->getUsername(), $name);
            return $this->render('gallery.html.twig', ['name' => $name, 'images' => $images]);
            
        } else {
            return $this->redirectToRoute('code_error', [
                'code' => '404'
            ]);
        }
    }
}
