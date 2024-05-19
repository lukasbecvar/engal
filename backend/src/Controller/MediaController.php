<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Schema;
use App\Manager\StorageManager;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Parameter;
use App\Manager\AuthTokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    /**
     * Retrieve the content associated with the provided token.
     *
     * This method fetches the content file corresponding to the given token, checks its validity,
     * and returns a streamed response with the file content if the token is valid.
     * If the token is missing or the associated media is not found, it returns a JSON response
     * with an appropriate error message and status code.
     *
     * @param Request $request The current request object.
     *
     * @return mixed A streamed response with the content file, or a JSON response with an error message.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success photo content resource')]
    #[Response(response: 400, description: 'The token parameter not found in request')]
    #[Response(response: 404, description: 'The media not found error')]
    #[Parameter(name: 'media_token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Parameter(name: 'auth_token', in: 'query', schema: new Schema(type: 'string'), description: 'User auth token', required: true)]
    #[Route('/api/media/content', methods: ['GET'], name: 'api_media_content')]
    public function getContent(Request $request, StorageManager $storageManager, AuthTokenManager $authTokenManager, UserManager $userManager): mixed
    {
        // get auth token from request
        $authToken = $request->get('auth_token');

        // get media token from request
        $mediaToken = $request->get('media_token');

        // check if token set
        if (!isset($mediaToken) || !isset($authToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'auth_token & media_token parameter is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check if token is valid
        if (!$authTokenManager->isTokenValid($authToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
                'message' => 'invalid JWT token'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // get user id form auth token
        $username = $authTokenManager->decodeToken($authToken)['username'];
        $userId = $userManager->getUserRepo($username)->getId();

        // check if media exist
        if (!$storageManager->isMediaExist($userId, $mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'media token: ' . $mediaToken . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // get content
        $filePath = $storageManager->getMediaFile($userId, $mediaToken);

        // create a StreamedResponse
        $response = new StreamedResponse(function () use ($filePath) {
            $handle = fopen($filePath, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 1024);
                flush();
            }
            fclose($handle);
        });

        // set headers
        $response->headers->set('Content-Type', $storageManager->getMediaType($mediaToken));
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', (string) filesize($filePath));

        return $response;
    }
}
