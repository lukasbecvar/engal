<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploaderController extends AbstractController
{
    private LogManager $logManager;
    private UserManager $userManager;
    private SecurityUtil $securityUtil;

    public function __construct(
        LogManager $logManager, 
        UserManager $userManager, 
        SecurityUtil $securityUtil
    ) {
        $this->logManager = $logManager;
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
    }

    #[Route('/media/upload', methods:['POST'], name: 'app_media_upload')]
    public function main(Request $request): Response
    {
        // upload storage directory
        $storage_directory = __DIR__.'/../../'.$_ENV['STORAGE_DIR_NAME'].'/';
        
        // get post data
        $token = $request->request->get('token');
        $gallery = $request->request->get('gallery');

        // check if request is post
        if (!$request->isMethod('POST')) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Post request required'
            ], 200);
        }

        // check if token seted
        if ($token == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: token'
            ], 200);
        }

        // check if gallery seted
        if ($gallery == null) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: gallery'
            ], 200);
        }
        
        // check if image seted
        if (empty($_FILES['image'])) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Required post data: image file'
            ], 200);
        }

        // escape user token
        $token = $this->securityUtil->escapeString($token);

        // check if user found in database
        if ($this->userManager->getUserRepository(['token' => $token]) != null) {
                
            // get uploaded file
            $uploaded_file = $_FILES['image'];
                
            // list of allowend media fromats
            $allowed_formats = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

            // check if media format allowed
            if (!in_array($uploaded_file['type'], $allowed_formats)) {
                return $this->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Unsuported format: allowed formats is: jpg, jpeg, png, gif'
                ], 200);
            } else {

                // create storage dir
                if (!file_exists($storage_directory)) {
                    mkdir($storage_directory);
                }

                // check if storage is writable
                if (!is_writable($storage_directory)) {
                    return $this->json([
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'Upload error: storage is not writable'
                    ], 200);
                } else {

                    // get username
                    $username = $this->userManager->getUsernameByToken($token);

                    // create user path 
                    if (!file_exists($storage_directory.$username)) {
                        mkdir($storage_directory.$username);
                    }

                    // create gallery dir
                    if (!file_exists($storage_directory.$username.'/'.$gallery)) {
                        mkdir($storage_directory.$username.'/'.$gallery);
                    }

                    $max_file_size_value = intval($_ENV['MAX_FILE_SIZE']);

                    // calculate maximal file size
                    $max_file_size = $max_file_size_value * 1024 * 1024;

                    // check file size limit
                    if ($uploaded_file['size'] > $max_file_size) {
                        return $this->json([
                            'status' => 'error',
                            'code' => 200,
                            'message' => 'Maximal file size is '.$max_file_size_value.'MB'
                        ], 200);
                    }

                    try {
                        // get file name
                        $file_name = $uploaded_file['name'];

                        // build final upload path
                        $destination = $storage_directory.$username.'/'.$gallery.'/'.$file_name;
                                        
                        // move file to upload dir
                        move_uploaded_file($uploaded_file['tmp_name'], $destination);
                            
                        // log action
                        $this->logManager->log('uploader', 'user: '.$username.' upload new media: '.$file_name.' to gallery: '.$gallery);
                        
                        return $this->json([
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Image uploaded to gallery: '.$gallery
                        ], 200);
                    } catch (\Exception $e) {
                        return $this->json([
                            'status' => 'error',
                            'code' => 500,
                            'message' => 'Error to upload image: '.$e->getMessage()
                        ], 200);
                    }
                }
            }
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token value'
            ], 200);
        }
    }
}
