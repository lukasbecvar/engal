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
use Symfony\Component\Routing\Attribute\Route;
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
    private UserManager $userManager;
    private SecurityUtil $securityUtil;
    private StorageManager $storageManager;
    private MediaRepository $mediaRepository;
    private AuthTokenManager $authTokenManager;

    public function __construct(
        UserManager $userManager,
        SecurityUtil $securityUtil,
        StorageManager $storageManager,
        MediaRepository $mediaRepository,
        AuthTokenManager $authTokenManager
    ) {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->storageManager = $storageManager;
        $this->mediaRepository = $mediaRepository;
        $this->authTokenManager = $authTokenManager;
    }

    /**
     * Retrieve the content associated with the provided token
     *
     * This method fetches the content file corresponding to the given token, checks its validity,
     * and returns a streamed response with the file content if the token is valid
     * If the token is missing or the associated media is not found, it returns a JSON response
     * with an appropriate error message and status code
     *
     * @param Request $request The current request object
     *
     * @return mixed A streamed response with the content file, or a JSON response with an error message
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success photo content resource')]
    #[Response(response: 400, description: 'The token parameter not found in requets')]
    #[Response(response: 404, description: 'The media not found error')]
    #[Parameter(name: 'media_token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Route('/api/media/content', methods: ['GET'], name: 'api_media_content')]
    public function getContent(Request $request): mixed
    {
        // get auth token from request
        $authToken = $this->authTokenManager->getTokenFromRequest($request);

        // get media token from request
        $mediaToken = $request->query->get('media_token');

        // check if token set
        if (!isset($mediaToken) || empty($authToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'auth token & media_token parameter is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check if token is valid
        if (!$this->authTokenManager->isTokenValid($authToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
                'message' => 'invalid JWT token'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // get user id from auth token
        $username = $this->authTokenManager->decodeToken($authToken)['username'];
        $userId = $this->userManager->getUserRepo($username)->getId();

        // check if media exists
        if (!$this->storageManager->isMediaExist($userId, $mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'media token: ' . $mediaToken . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // get file path
        $filePath = $this->storageManager->getMediaFile($userId, $mediaToken);
        $meta = $this->storageManager->getMediaFileMeta($userId, $mediaToken);
        $downloadFileName = $meta !== null ? $mediaToken . '.' . $meta['extension'] : basename((string) $filePath);

        // Read file content
        $decryptedContent = file_get_contents($filePath);

        // create a StreamedResponse with decrypted content
        $response = new StreamedResponse(function () use ($decryptedContent) {
            echo $decryptedContent;
        });

        // set headers
        $contentType = $this->storageManager->getMediaType($mediaToken) ?? 'application/octet-stream';
        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Disposition', 'inline; filename="' . $downloadFileName . '"');
        if (str_contains($contentType, 'video')) {
            $response->headers->set('Cache-Control', 'no-store');
        } else {
            $response->headers->set('Cache-Control', 'public, max-age=604800');
        }
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', (string) strlen($decryptedContent));

        // return streamed response
        return $response;
    }

    /**
     * Retrieve the media information associated with the provided token
     *
     * This method fetches the media information corresponding to the given token and returns a JSON response with the media details
     * If the token is missing or the associated media is not found, it returns a JSON response with an appropriate error message and status code
     *
     * @param Request $request The current request object
     * @param Security $security The security component to get the logged-in user
     *
     * @return JsonResponse A JSON response with the media information or an error message
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'The success media info response')]
    #[Response(response: 400, description: 'The media_token parameter not found in request')]
    #[Response(response: 404, description: 'The media not found error')]
    #[Parameter(name: 'media_token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Route('/api/media/info', methods: ['GET'], name: 'api_media_info')]
    public function getMediaInfo(Request $request, Security $security): JsonResponse
    {
        // get logged user ID
        $userId = $this->userManager->getUserData($security)->getId();

        $mediaToken = $request->query->get('media_token');

        // check if media token is set
        if (!isset($mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'media_token parameter is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // get media info
        $media = $this->mediaRepository->findOneBy(['token' => $mediaToken, 'owner_id' => $userId]);

        // check if media exist
        if ($media == null) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'media token: ' . $mediaToken . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $name = $this->securityUtil->decryptName((string) $media->getName());

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

    /**
     * Return a presigned URL for direct media access (S3/MinIO)
     *
     * @param Request $request The current request object
     * @param Security $security The security component to get the logged-in user
     *
     * @return JsonResponse A JSON response with the media information or an error message
     */
    #[Tag(name: "Resources")]
    #[Response(response: 200, description: 'Presigned URL for media content')]
    #[Response(response: 400, description: 'Missing media_token parameter')]
    #[Response(response: 404, description: 'Media not found')]
    #[Parameter(name: 'media_token', in: 'query', schema: new Schema(type: 'string'), description: 'Media token', required: true)]
    #[Route('/api/media/presigned', methods: ['GET'], name: 'api_media_presigned')]
    public function getPresignedUrl(Request $request, Security $security): JsonResponse
    {
        $userId = $this->userManager->getUserData($security)->getId();
        $mediaToken = $request->query->get('media_token');
        $ttl = $request->query->getInt('ttl', $this->storageManager->getDefaultPresignedTtl());
        $ttl = max(300, min($ttl, 3600));

        // check if media token is set
        if (!isset($mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'media_token parameter is required'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check if media exist
        if (!$this->storageManager->isMediaExist($userId, $mediaToken)) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'media token: ' . $mediaToken . ' not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // generate presigned url
        $url = $this->storageManager->generatePresignedUrl($userId, $mediaToken, $ttl);
        if ($url === null) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'unable to generate presigned url'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        // return success response
        return $this->json([
            'status' => 'success',
            'code' => JsonResponse::HTTP_OK,
            'expires_in' => $ttl,
            'url' => $url
        ], JsonResponse::HTTP_OK);
    }
}
