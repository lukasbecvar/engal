<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Schema;
use App\Manager\StorageManager;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Parameter;
use App\Manager\ThumbnailManager;
use App\Repository\MediaRepository;
use App\Message\PreloadThumbnailsMessage;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response as ContentResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ThumbnailController
 *
 * Thumbnail controller for media resources
 *
 * @package App\Controller
 */
class ThumbnailController extends AbstractController
{
    private UserManager $userManager;
    private StorageManager $storageManager;
    private MediaRepository $mediaRepository;
    private ThumbnailManager $thumbnailManager;

    public function __construct(UserManager $userManager, StorageManager $storageManager, MediaRepository $mediaRepository, ThumbnailManager $thumbnailManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
        $this->mediaRepository = $mediaRepository;
        $this->thumbnailManager = $thumbnailManager;
    }

    /**
     * Retrieves the thumbnail of a media resource based on provided parameters.
     *
     * Return minified media thumbnail for render in gallery browser
     *
     * @param Security $security The security service for handling user authentication.
     * @param Request $request The HTTP request object containing query parameters.
     *
     * @return ContentResponse The response containing the thumbnail image content.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success photo thumbnail resource type jpg')]
    #[Response(response: 400, description: 'The token, width or height parameters not found in requets')]
    #[Response(response: 404, description: 'The media not found error')]
    #[Parameter(name: 'token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Route(['/api/thumbnail'], methods: ['GET'], name: 'api_media_thumbnail')]
    public function getThumbnail(Security $security, Request $request): ContentResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // get data from token
        $token = $request->get('token');

        // check if token set
        if (!isset($token)) {
            return $this->json([
               'status' => 'error',
               'code' => JsonResponse::HTTP_BAD_REQUEST,
               'message' => 'token parameter is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check if media exist
        if (!$this->storageManager->isMediaExist($userId, $token)) {
            return $this->json([
               'status' => 'error',
               'code' => JsonResponse::HTTP_NOT_FOUND,
               'message' => 'media token: ' . $token . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // get content
        $content = $this->thumbnailManager->getMediaThumbnail($userId, $token);

        // create a streamed response with image thumbnail content
        return new StreamedResponse(function () use ($content) {
            echo $content;
        }, ContentResponse::HTTP_OK, [
            'Content-Type' => 'image/jpg',
        ]);
    }

    /**
     * Preload app thumbnails.
     *
     * Push thumbnails preload message to doctrine queue for async process
     *
     * @param Request $request The HTTP request object containing query parameters.
     * @param Security $security The security service for handling user authentication.
     * @param MessageBusInterface $messageBus The symfony messenger async dispatcher.
     *
     * @return JsonResponse The JSON response indicating the status of the operation.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Preload command run success')]
    #[Response(response: 500, description: 'Preload command run error')]
    #[Parameter(name: 'gallery_name', in: 'query', schema: new Schema(type: 'string'), description: 'Gallery name for preload specific gallery thumbnails')]
    #[Route(['/api/thumbnail/preload'], methods: ['GET'], name: 'api_media_preload_thumbnails')]
    public function preloadThumbnails(Request $request, Security $security, MessageBusInterface $messageBus): JsonResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // get gallery name form request parameter
        $galleryName = $request->get('gallery_name', null);

        // check if gallery exist
        if ($galleryName != null) {
            if (!$this->mediaRepository->isGalleryExists($userId, $galleryName)) {
                return $this->json([
                    'status' => 'error',
                    'code' => JsonResponse::HTTP_NOT_FOUND,
                    'message' => 'gallery: ' . $galleryName . ' not found in database'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        }

        try {
            // dispatch async process
            $message = new PreloadThumbnailsMessage($userId, $galleryName);
            $messageBus->dispatch($message);

            // return status
            return $this->json([
                'status' => 'success',
                'code' => JsonResponse::HTTP_OK,
                'message' => 'thumbnails preload process started successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'thumbnails preload process could not be started'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
