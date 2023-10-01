<?php

namespace App\Controller;

use App\Helper\LogHelper;
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

    #[Route(['/', '/gallery'], name: 'app_empty_gallery')]
    public function emptyGallery(): Response
    {
        return $this->redirectToRoute('code_error', [
            'code' => '400'
        ]);
    }

    #[Route(['/', '/gallery/_1337_all/{page}'], name: 'app_all_gallery')]
    public function allGallery($page): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // get gallery images
            $images = StorageUtil::getImagesContentAll($this->loginHelper->getUsername(), $page);

            $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed all galleries');
            return $this->render('gallery.html.twig', [
                'page' => $page,
                'limit' => $_ENV['LIMIT_PER_PAGE'],
                'name' => '_1337_all', 
                'images' => $images
            ]);

        }
    }

    
    #[Route(['/', '/gallery/_1337_random'], name: 'app_random_gallery')]
    public function randomGallery(): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // get gallery images
            $images = StorageUtil::getImagesContentAll($this->loginHelper->getUsername(), 1, 'random_sort');

            $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed all galleries');
            return $this->render('gallery.html.twig', [
                'page' => 1,
                'limit' => $_ENV['LIMIT_PER_PAGE'],
                'name' => '_1337_random', 
                'images' => $images
            ]);

        }
    }
    

    #[Route(['/', '/gallery/{name}/{page}'], name: 'app_gallery')]
    public function gallery($name, $page): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // escape name
            $name = EscapeUtil::special_chars_strip($name);

            // check if gallery exist
            if (StorageUtil::checkGallery($this->loginHelper->getUsername(), $name)) {
                
                // get gallery images
                $images = StorageUtil::getImagesContent($this->loginHelper->getUsername(), $name, $page);

                $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed gallery: '.$name);
                return $this->render('gallery.html.twig', [
                    'page' => $page,
                    'limit' => $_ENV['LIMIT_PER_PAGE'],
                    'name' => $name, 
                    'images' => $images
                ]);
                
            } else {
                return $this->redirectToRoute('code_error', [
                    'code' => '404'
                ]);
            }

        }
    }
}
