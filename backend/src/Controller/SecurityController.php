<?php

namespace App\Controller;

use OpenApi\Attributes\Tag;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use OpenApi\Attributes\Response;
use App\Manager\AuthTokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;

/**
 * Class SecurityController
 *
 * Controller handling security-related actions such as logout.
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    private LogManager $logManager;
    private ErrorManager $errorManager;
    private AuthTokenManager $authTokenManager;

    /**
     * SecurityController constructor.
     *
     * @param LogManager $logManager The log manager.
     * @param ErrorManager $errorManager The error manager.
     * @param AuthTokenManager $authTokenManager The auth token manager.
     */
    public function __construct(LogManager $logManager, ErrorManager $errorManager, AuthTokenManager $authTokenManager) {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->authTokenManager = $authTokenManager;
    }

    /**
     * Logout action.
     *
     * Logs out the user and blacklists the old authentication token.
     *
     * @param Request $request The HTTP request.
     * @param SecurityBundleSecurity $security The security bundle security.
     * @return JsonResponse The JSON response.
     */
    #[Tag(name: "Auth")]
    #[Response(response: 200, description: 'The logout successful message')]
    #[Response(response: 401, description: 'The JWT token Invalid message')]
    #[Response(response: 500, description: 'The logout error message')]
    #[Route('/api/logout', name: 'api_security_logout', methods: ['POST'])]
    public function logout(Request $request, SecurityBundleSecurity $security): JsonResponse
    {
        // get user
        $user = $security->getUser();

        // get auth token
        $token = $this->authTokenManager->getTokenFromRequest($request);

        try {
            // log logout
            $this->logManager->log('authenticator', 'User '.$user->getUserIdentifier().' logged out');

            // blacklist old token
            $this->authTokenManager->blacklistToken($token);

            // return response
            return new JsonResponse([
                'status' => 'success',
                'code' => 200,
                'message' => 'Logout successful'
            ], 200);
        } catch (\Exception $e) {
            return $this->errorManager->handleError('logout error: '.$e->getMessage(), 500);
        }
    }
}
