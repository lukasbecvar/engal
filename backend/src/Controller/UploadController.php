<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Manager\ErrorManager;
use App\Manager\StorageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadController extends AbstractController
{
    private UserManager $userManager;
    private ErrorManager $errorManager;
    private StorageManager $storageManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserManager $userManager, 
        ErrorManager $errorManager, 
        StorageManager $storageManager, 
        EntityManagerInterface $entityManager
    ) {
        $this->userManager = $userManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->storageManager = $storageManager;
    }

    #[Route('/api/upload/config/policy', methods: ['GET'], name: 'api_file_upload_policy')]
    public function uploadConfigPolicy(): JsonResponse
    {        
        return $this->json([
            'FILE_UPLOAD_STATUS' => $_ENV['FILE_UPLOAD_STATUS'],
            'MAX_FILES_COUNT' => $_ENV['MAX_FILES_COUNT'],
            'MAX_FILES_SIZE' => $_ENV['MAX_FILES_SIZE'],
            'MAX_GALLERY_NAME_LENGTH' => $_ENV['MAX_GALLERY_NAME_LENGTH'],
            'ALLOWED_FILE_EXTENSIONS' => json_decode($_ENV['ALLOWED_FILE_EXTENSIONS'], true)
        ], 200);
    }

    #[Route('/api/upload', methods: ['POST'], name: 'api_file_upload')]
    public function fileUpload(Request $request, Security $security): JsonResponse
    {
        // get files from request
        $uploaded_files = $request->files->get('files');

        // get gallery name from request
        $gallery_name = $request->get('gallery_name');
    
        // max files count check
        $total_files = count($uploaded_files);
        if ($total_files > intval($_ENV['MAX_FILES_COUNT'])) {
            return $this->json([
                    'status' => 'error',
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'maximum number of allowable file uploads (2000) has been exceeded.'
            ], Response::HTTP_BAD_REQUEST);
        }
    
        // max files size check
        $max_file_size_bytes = $_ENV['MAX_FILES_SIZE'] * 1024 * 1024 * 1024; // get GB
        foreach ($uploaded_files as $file) {
            if ($file instanceof UploadedFile && $file->getSize() > $max_file_size_bytes) {
                return $this->json([
                    'status' => 'error',
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'maximum file size (20 GB) has been exceeded for file: '.$file->getClientOriginalName()
                ], Response::HTTP_BAD_REQUEST);
            }
        }
            
        // file extensions check
        $allowed_file_extensions = json_decode($_ENV['ALLOWED_FILE_EXTENSIONS'], true);
        foreach ($uploaded_files as $file) {
            $fileExtension = $file->getClientOriginalExtension();
            if (!in_array($fileExtension, $allowed_file_extensions)) {
                return $this->json([
                    'status' => 'error',
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'file '.$file->getClientOriginalName().' has an invalid extension.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    
        // store files data
        $this->entityManager->beginTransaction(); // start upload transaction

        try {
            foreach ($uploaded_files as $file) {
                // store media entity data
                $token = $this->storageManager->storeMediaEntity([
                    'name' => $file->getClientOriginalName(),
                    'gallery_name' => $gallery_name,
                    'type' => $file->getMimeType(),
                    'owner_id' => $this->userManager->getUserData($security)->getID(),
                    'upload_time' => date('d.m.Y H:i:s'),
                ]);

                // store media file
                $this->storageManager->storeMediaFile($token, $file, $security);
            }

            $this->entityManager->commit(); // commit transaction 
        } catch (\Exception $e) {
            $this->entityManager->rollback(); 
            $this->errorManager->handleError('error to upload media: '.$e->getMessage(), 500);
        }
    
        // return success message
        return $this->json([
            'status' => 'success',
            'code' => Response::HTTP_OK,
            'message' => 'files uploaded successfully'
        ], Response::HTTP_OK);
    }
}
