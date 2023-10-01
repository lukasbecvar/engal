<?php

namespace App\Controller;

use App\Helper\ErrorHelper;
use App\Util\EscapeUtil;
use App\Util\StorageUtil;
use App\Helper\LogHelper;
use App\Helper\LoginHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Gallery controller provides gallery browser component
*/

class GalleryController extends AbstractController
{
    private $logHelper;
    private $loginHelper;
    private $errorHelper;

    public function __construct(LoginHelper $loginHelper, LogHelper $logHelper, ErrorHelper $errorHelper)
    {
        $this->logHelper = $logHelper;
        $this->loginHelper = $loginHelper;
        $this->errorHelper = $errorHelper;
    }

    // gallery with empty nam protect
    #[Route(['/gallery'], name: 'app_empty_gallery')]
    public function emptyGallery(): Response
    {
        return $this->redirectToRoute('code_error', [
            'code' => '400'
        ]);
    }

    // gallery with all images
    #[Route(['/gallery/_1337_all/{page}'], methods: ['GET'], name: 'app_all_gallery')]
    public function allGallery($page): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // get gallery images
            $images = StorageUtil::getImagesContentAll($this->loginHelper->getUsername(), $page);

            // log to database
            $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed all galleries');
            
            // return gallery view
            return $this->render('gallery.html.twig', [
                'page' => $page,
                'limit' => $_ENV['LIMIT_PER_PAGE'],
                'name' => '_1337_all', 
                'images' => $images
            ]);

        }
    }

    // gallery sort random
    #[Route(['/gallery/_1337_random'], name: 'app_random_gallery')]
    public function randomGallery(): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // get gallery images
            $images = StorageUtil::getImagesContentAll($this->loginHelper->getUsername(), 1, 'random_sort');

            // log to databas
            $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed all galleries with random sort');
            
            // return gallery view
            return $this->render('gallery.html.twig', [
                'page' => 1,
                'limit' => $_ENV['LIMIT_PER_PAGE'],
                'name' => '_1337_random', 
                'images' => $images
            ]);

        }
    }
    
    // main gallery browser (where name & page)
    #[Route(['/gallery/{name}/{page}'],  methods: ['GET'], name: 'app_gallery')]
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

                // log to database
                $this->logHelper->log('gallery', $this->loginHelper->getUsername().' viewed gallery: '.$name);
                
                // return gallery view
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

    #[Route(['/gallery/delete/{gallery_name}/{image_name}'],  methods: ['GET'], name: 'delete_image')]
    public function delete($gallery_name, $image_name): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // get file data
            $storage_name = $this->loginHelper->getUsername();
            $gallery_name = EscapeUtil::special_chars_strip($gallery_name);
            $image_name = EscapeUtil::special_chars_strip($image_name);

            // build file path
            $path = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name.'/'.$image_name.'.image';

            // check if file exist
            if (file_exists($path)) {

                // delete file & check if valid
                if (unlink($path)) {

                    // check if gallery empty
                    if (StorageUtil::isGalleryEmpty($storage_name, $gallery_name)) {

                        // build gallery path
                        $gallery_path = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name;

                        // delete empty gallery
                        try {    
                            rmdir($gallery_path);
                        } catch (\Exception $e) {
                            $this->errorHelper->handleError('error to delete empty gallery: '.$e->getMessage(), 500);
                        }

                        // redirect to homr after delete empty gallery
                        return $this->redirectToRoute('app_home');
                    }

                    // redirect back to gallery
                    return $this->redirectToRoute('app_gallery', [
                        'name' => $gallery_name,
                        'page' => 1
                    ]);

                } else {
                    $this->errorHelper->handleError('file: '.$path.' delete error: unexpected', 500);
                }
            } else {
                $this->errorHelper->handleError('file: '.$path.' delete error: not found', 404);
            }

            $this->redirectToRoute('app_home');
        }
    }
}
