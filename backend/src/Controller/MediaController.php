<?php

namespace App\Controller;

use App\Util\SecurityUtil;
use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use OpenApi\Attributes\Schema;
use App\Manager\StorageManager;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Parameter;
use App\Manager\AuthTokenManager;
use App\Repository\MediaRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
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
     *
     * @return mixed A streamed response with the content file, or a JSON response with an error message.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success photo content resource')]
    #[Response(response: 400, description: 'The token parameter not found in requets')]
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

        // get user id from auth token
        $username = $authTokenManager->decodeToken($authToken)['username'];
        $userId = $userManager->getUserRepo($username)->getId();

        // check if media exists
        if (!$storageManager->isMediaExist($userId, $mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'media token: ' . $mediaToken . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // get file path
        $filePath = $storageManager->getMediaFile($userId, $mediaToken);

        // Read encrypted file content
        $encryptedContent = file_get_contents($filePath);

        // Decrypt file content if encrypted
        $decryptedContent = $this->securityUtil->decryptAES($encryptedContent);

        // create a StreamedResponse with decrypted content
        $response = new StreamedResponse(function () use ($decryptedContent) {
            echo $decryptedContent;
        });

        // set headers
        $response->headers->set('Content-Type', $storageManager->getMediaType($mediaToken));
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', (string) strlen($decryptedContent));

        return $response;
    }



    /**
     * Retrieve the media information associated with the provided token.
     *
     * This method fetches the media information corresponding to the given token and returns a JSON response with the media details.
     * If the token is missing or the associated media is not found, it returns a JSON response with an appropriate error message and status code.
     *
     * @param MediaRepository $mediaRepository The repository to access media data.
     * @param Request $request The current request object.
     * @param UserManager $userManager The manager to handle user data.
     * @param Security $security The security component to get the logged-in user.
     *
     * @return JsonResponse A JSON response with the media information or an error message.
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success media info response')]
    #[Response(response: 400, description: 'The media_token parameter not found in request')]
    #[Response(response: 404, description: 'The media not found error')]
    #[Parameter(name: 'media_token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Route('/api/media/info', methods: ['GET'], name: 'api_media_info')]
    public function getMediaInfo(MediaRepository $mediaRepository, Request $request, UserManager $userManager, Security $security): JsonResponse
    {
        // get logged user ID
        $userId = $userManager->getUserData($security)->getId();

        $mediaToken = $request->get('media_token');

        // check if media token is set
        if (!isset($mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'media_token parameter is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // get media info
        $media = $mediaRepository->findOneBy(['token' => $mediaToken, 'owner_id' => $userId]);

        // check if media exist
        if ($media == null) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'media token: ' . $mediaToken . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // decrypt name
        $name = $this->securityUtil->decryptAES($media->getName());

        // get media info array
        $mediaInfo = [
            'id' => $media->getId(),
            'name' => $name,
            'token' => $media->getToken(),
            'type' => $media->getType(),
            'length' => $media->GetLength(),
            'owner_id' => $media->getOwnerId(),
            'upload_time' => $media->getUploadTime(),
            'last_edit_time' => $media->getLastEditTime()
        ];

        // return media info response
        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'media_info' => $mediaInfo
        ], JsonResponse::HTTP_OK);
    }
}
