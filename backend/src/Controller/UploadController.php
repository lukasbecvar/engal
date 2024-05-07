<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController extends AbstractController
{
    #[Route('/api/upload', methods: ['POST'], name: 'api_file_upload')]
    public function fileUpload(Request $request): Response
    {
        $uploadedFiles = $request->files->get('files');
    
        // max files count check
        $maxFileCount = 2000;
        $totalFiles = count($uploadedFiles);
        if ($totalFiles > intval($maxFileCount)) {
            return $this->json(['error' => 'Maximum number of allowable file uploads (2000) has been exceeded.'], Response::HTTP_BAD_REQUEST);
        }
    
        // max files size check
        $maxFileSizeBytes = 8 * 1024 * 1024 * 1024; // 20 GB
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
                $file->move(__DIR__.'/../../storage/', $file->getClientOriginalName());
            } catch (\Exception $e) {
                return $this->json(['error' => 'Failed to move file: '.$file->getClientOriginalName()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    
        return $this->json(['message' => 'Files uploaded successfully'], Response::HTTP_OK);
    }
    
    
    
}
