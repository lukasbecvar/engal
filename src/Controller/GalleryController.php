<?php

namespace App\Controller;

use App\Util\EscapeUtil;
use App\Helper\LogHelper;
use App\Helper\LoginHelper;
use App\Helper\ErrorHelper;
use App\Helper\StorageHelper;
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
    private $storageHelper;

    public function __construct(
        LogHelper $logHelper, 
        LoginHelper $loginHelper, 
        ErrorHelper $errorHelper,
        StorageHelper $storageHelper,
    ) {
        $this->logHelper = $logHelper;
        $this->loginHelper = $loginHelper;
        $this->errorHelper = $errorHelper;
        $this->storageHelper = $storageHelper;
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

            // get current username
            $username = $this->loginHelper->getUsername();

            // get gallery images
            $images = $this->storageHelper->getImagesContentAll($username, $page);

            // log to database
            $this->logHelper->log('gallery', $username.' viewed all galleries');
            
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

            // get current username
            $username = $this->loginHelper->getUsername();

            // get gallery images
            $images = $this->storageHelper->getImagesContentAll($username, 1, 'random_sort');

            // log to databas
            $this->logHelper->log('gallery', $username.' viewed all galleries with random sort');
            
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

            // get current username
            $username = $this->loginHelper->getUsername();

            // escape name
            $name = EscapeUtil::special_chars_strip($name);

            // check if gallery exist
            if ($this->storageHelper->checkGallery($username, $name)) {
                
                // get gallery images
                $images = $this->storageHelper->getImagesContent($username, $name, $page);

                // log to database
                $this->logHelper->log('gallery', $username.' viewed gallery: '.$name);
                
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

    // delete image request
    #[Route(['/gallery/delete/{gallery_name}/{image_name}'],  methods: ['GET'], name: 'delete_image')]
    public function imageDelete($gallery_name, $image_name): Response
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

                    // log file delete
                    $this->logHelper->log('delete', $this->loginHelper->getUsername().' delete image: '.$image_name.' in gallery: '.$gallery_name);

                    // check if gallery empty
                    if ($this->storageHelper->isGalleryEmpty($storage_name, $gallery_name)) {

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
