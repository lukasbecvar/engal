<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Schema;
use App\Manager\StorageManager;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Parameter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class MediaController
 *
 * Main media content get controller
 *
 * @package App\Controller
 */
class MediaController extends AbstractController
{
    private UserManager $userManager;
    private StorageManager $storageManager;

    public function __construct(UserManager $userManager, StorageManager $storageManager)
    {
        $this->userManager = $userManager;
        $this->storageManager = $storageManager;
    }

    /**
     * Retrieve the content associated with the provided token.
     *
     * This method fetches the content file corresponding to the given token, checks its validity,
     * and returns a streamed response with the file content if the token is valid.
     * If the token is missing or the associated media is not found, it returns a JSON response
     * with an appropriate error message and status code.
     *
     * @param Request $request The current request object.
     * @param Security $security The security service for accessing user data.
     *
     * @return mixed A streamed response with the content file, or a JSON response with an error message.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success photo content resource')]
    #[Response(response: 400, description: 'The token parameter not found in requets')]
    #[Response(response: 404, description: 'The media not found error')]
    #[Parameter(name: 'token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Route('/api/media/content', methods: ['GET'], name: 'api_media_content')]
    public function getContent(Request $request, Security $security): mixed
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        // get token from request
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
        $content = $this->storageManager->getMediaFile($userId, $token);

        // assuming $content is the file path
        $response = new BinaryFileResponse($content);

        // set headers
        $response->headers->set('Content-Type', $this->storageManager->getMediaType($token));
        $response->headers->set('Content-Disposition', 'inline');
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', '-1');

        // add additional headers for caching
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($content));

        return $response;
    }
}
