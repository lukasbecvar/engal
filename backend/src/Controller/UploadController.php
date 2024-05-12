<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use App\Manager\ErrorManager;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Schema;
use App\Manager\StorageManager;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\RequestBody;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UploadController
 *
 * Media upload controller api
 *
 * @package App\Controller
 */
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

    /**
     * Get upload policy config
     *
     * @Route("/config/policy", methods={"GET"}, name="api_file_upload_policy")
     *
     * @return JsonResponse
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Get upload policy config')]
    #[Route('/api/upload/config/policy', methods: ['GET'], name: 'api_file_upload_policy')]
    public function uploadConfigPolicy(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'policy' => [
                'FILE_UPLOAD_STATUS' => $_ENV['FILE_UPLOAD_STATUS'],
                'MAX_FILES_COUNT' => $_ENV['MAX_FILES_COUNT'],
                'MAX_FILES_SIZE' => $_ENV['MAX_FILES_SIZE'],
                'MAX_GALLERY_NAME_LENGTH' => $_ENV['MAX_GALLERY_NAME_LENGTH'],
                'ALLOWED_FILE_EXTENSIONS' => json_decode($_ENV['ALLOWED_FILE_EXTENSIONS'], true)
            ]
        ], JsonResponse::HTTP_OK);
    }

    /**
     * File upload endpoint
     *
     * @param Request $request
     * @param Security $security
     * @return JsonResponse
     */
    #[Tag(name: "Resources")]
    #[RequestBody(
        content: [
            new MediaType(
                mediaType: "multipart/form-data",
                schema: new Schema(properties: [
                    new Property(
                        property: "gallery_name",
                        type: "string",
                        description: "Media gallery name",
                    ),
                    new Property(
                        property: "files[]",
                        type: "array",
                        description: "Files to upload",
                        items: new Items(type: "string", format: "binary")
                    )
                ])
            )
        ]
    )]
    #[Response(response: 200, description: 'File upload success message')]
    #[Response(response: 400, description: 'Bad data request error')]
    #[Response(response: 500, description: 'Internal upload error')]
    #[Route('/api/upload', methods: ['POST'], name: 'api_file_upload')]
    public function fileUpload(Request $request, Security $security): JsonResponse
    {
        // get files from request
        $uploadedFiles = $request->files->get('files');

        // get gallery name from request
        $galleryName = $request->get('gallery_name');

        // check gallery name set
        if (empty($galleryName)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'your gallery name is empty'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check gallery name lenth
        if (strlen($galleryName) > intval($_ENV['MAX_GALLERY_NAME_LENGTH'])) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'maximal gallery name length is ' . $_ENV['MAX_GALLERY_NAME_LENGTH']
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check file input set
        if ($uploadedFiles == null) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'your files input is empty'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // max files count check
        $total_files = count($uploadedFiles);
        if ($total_files > intval($_ENV['MAX_FILES_COUNT'])) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'maximum number of allowable file uploads (2000) has been exceeded.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // max files size check
        $maxFileSizeBytes = $_ENV['MAX_FILES_SIZE'] * 1024 * 1024 * 1024; // get GB
        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile && $file->getSize() > $maxFileSizeBytes) {
                return $this->json([
                    'status' => 'error',
                    'code' => JsonResponse::HTTP_BAD_REQUEST,
                    'message' => 'maximum file size (20 GB) has been exceeded for file: ' . $file->getClientOriginalName()
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // file extensions check
        $allowedFileExtensions = json_decode($_ENV['ALLOWED_FILE_EXTENSIONS'], true);
        foreach ($uploadedFiles as $file) {
            $fileExtension = $file->getClientOriginalExtension();
            if (!in_array($fileExtension, $allowedFileExtensions)) {
                return $this->json([
                    'status' => 'error',
                    'code' => JsonResponse::HTTP_BAD_REQUEST,
                    'message' => 'file ' . $file->getClientOriginalName() . ' has an invalid extension.'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // store files data
        $this->entityManager->beginTransaction(); // start upload transaction

        try {
            foreach ($uploadedFiles as $file) {
                // get owner ID
                $ownerId = $this->userManager->getUserData($security)->getID();

                // store media entity data
                $token = $this->storageManager->storeMediaEntity([
                    'name' => $file->getClientOriginalName(),
                    'gallery_name' => $galleryName,
                    'type' => $file->getMimeType(),
                    'owner_id' => $ownerId,
                    'upload_time' => date('d.m.Y H:i:s'),
                ]);

                // store media file
                $this->storageManager->storeMediaFile($token, $file, $ownerId);
            }

            $this->entityManager->commit(); // commit transaction
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->errorManager->handleError('error to upload media: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        // return success message
        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'message' => 'files uploaded successfully'
        ], JsonResponse::HTTP_OK);
    }
}
