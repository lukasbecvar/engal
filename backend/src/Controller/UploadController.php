<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadController extends AbstractController
{
    #[Route('/api/upload', methods: ['POST'], name: 'api_file_upload')]
    public function fileUpload(Request $request): Response
    {
        $uploadedFiles = $request->files->get('files');
    
        // file count check
        $maxFileCount = 2000;
        $totalFiles = count($uploadedFiles);
        if ($totalFiles > $maxFileCount) {
            return $this->json(['error' => 'Maximum number of allowable file uploads (2000) has been exceeded.'], Response::HTTP_BAD_REQUEST);
        }
    
        // calculate file list size
        $totalSizeBytes = array_reduce($uploadedFiles, function ($total, $file) {
            return $total + $file->getSize();
        }, 0);
    
        // file list size check
        $maxFileSizeBytes = 20 * 1024 * 1024 * 1024; // 20 GB
        if (intval($totalSizeBytes) > intval($maxFileSizeBytes)) {
            return $this->json(['error' => 'Maximum file list size (20 GB) has been exceeded.'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!$uploadedFiles) {
            return $this->json(['error' => 'No files uploaded'], Response::HTTP_BAD_REQUEST);
        }
    
        // move uploaded files to appropriate directory
        foreach ($uploadedFiles as $file) {
            $file->move(__DIR__.'/../../storage/', $file->getClientOriginalName());
        }
    
        return $this->json(['message' => 'Files uploaded successfully'], Response::HTTP_OK);
    }
    
}
