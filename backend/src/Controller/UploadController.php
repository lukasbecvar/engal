<?php

namespace App\Controller;

use App\Entity\Media;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\ByteString;

class UploadController extends AbstractController
{
    #[Route('/api/upload/config/policy', methods: ['GET'], name: 'api_file_upload_policy')]
    public function uploadConfigPolicy(): JsonResponse
    {
        return $this->json([
            'FILE_UPLOAD_STATUS' => $_ENV['FILE_UPLOAD_STATUS'],
            'MAX_FILES_COUNT' => $_ENV['MAX_FILES_COUNT'],
            'MAX_FILES_SIZE' => $_ENV['MAX_FILES_SIZE'],
            'MAX_GALLERY_NAME_LENGTH' => $_ENV['MAX_GALLERY_NAME_LENGTH']
        ], 200);
    }

    #[Route('/api/upload', methods: ['POST'], name: 'api_file_upload')]
    public function fileUpload(Request $request, Security $security, EntityManagerInterface $entityManager, UserManager $userManager): JsonResponse
    {
        $uploadedFiles = $request->files->get('files');

        $gallery_name = $request->get('gallery_name');
    
        // max files count check
        $maxFileCount = $_ENV['MAX_FILES_COUNT'];
        $totalFiles = count($uploadedFiles);
        if ($totalFiles > intval($maxFileCount)) {
            return $this->json(['error' => 'Maximum number of allowable file uploads (2000) has been exceeded.'], Response::HTTP_BAD_REQUEST);
        }
    
        // max files size check
        $maxFileSizeBytes = $_ENV['MAX_FILES_SIZE'] * 1024 * 1024 * 1024; // 20 GB
        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile && $file->getSize() > $maxFileSizeBytes) {
                return $this->json(['error' => 'Maximum file size (20 GB) has been exceeded for file: '.$file->getClientOriginalName()], Response::HTTP_BAD_REQUEST);
            }
        }
    
        $allowedFileExtensions = [
            'jpeg', 
            'jpg', 
            'png', 
            'gif',
            
            'qt', 
            'mp4', 
            'm4p', 
            'm4v', 
            'amv', 
            'wmv',
            'mov', 
            'flv', 
            'm4v', 
            'mkv', 
            '3gp', 
            '3g2', 
            'avi', 
            'mpg', 
            'MP2T', 
            'webm', 
            'mpeg', 
            'x-m4v',
            'x-ms-asf',
            'x-ms-wmv', 
            'quicktime'
        ];
    
        // file extensions check
        foreach ($uploadedFiles as $file) {
            $fileExtension = $file->getClientOriginalExtension();
            if (!in_array($fileExtension, $allowedFileExtensions)) {
                return $this->json(['error' => 'File '.$file->getClientOriginalName().' has an invalid extension.'], Response::HTTP_BAD_REQUEST);
            }
        }
    
        // move uploaded file
        foreach ($uploadedFiles as $file) {

            try {
                $token = ByteString::fromRandom(32)->toString();

                $media = new Media();

                $media->setName($file->getClientOriginalName());
                $media->setGalleryName($gallery_name);
                $media->setType($file->getMimeType());
                $media->setOwnerId($userManager->getUserData($security)->getID());
                $media->setToken($token);
                $media->setUploadTime(date('d.m.Y H:i:s'));
                $media->setLastEditTime('non-edited');

                $entityManager->persist($media);
                $entityManager->flush();

            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }


            try {
                if (str_contains($file->getMimeType(), 'video')) {
                    $file->move(__DIR__.'/../../storage/'.$userManager->getUserData($security)->getID().'/videos', $file->getClientOriginalName());
                } else {
                    $file->move(__DIR__.'/../../storage/'.$userManager->getUserData($security)->getID().'/images', $file->getClientOriginalName());

                }
            } catch (\Exception $e) {
                return $this->json(['error' => 'Failed to move file: '.$file->getClientOriginalName()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        return $this->json(['message' => 'Files uploaded successfully'], Response::HTTP_OK);
    }
    
    
    
}
