<?php

namespace App\Controller\Auth;

use OpenApi\Attributes\Tag;
use App\Manager\UserManager;
use App\Util\VisitorInfoUtil;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Parameter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class RegisterController
 *
 * Controller handling user registration.
 *
 * @package App\Controller\Auth
 */
class RegisterController extends AbstractController
{
    private UserManager $userManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(UserManager $userManager, VisitorInfoUtil $visitorInfoUtil)
    {
        $this->userManager = $userManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Registers a new user.
     *
     * Register new user with provided username and password in request parameters
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The JSON response.
     */
    #[Tag(name: "Auth")]
    #[Response(response: 200, description: 'The success user register message')]
    #[Response(response: 400, description: 'Invalid request data message')]
    #[Response(response: 403, description: 'New register is disabled by server admin')]
    #[Response(response: 409, description: 'Username is already exist error')]
    #[Parameter(name: 'username', in: 'query', schema: new Schema(type: 'string'), description: 'New username', required: true)]
    #[Parameter(name: 'password', in: 'query', schema: new Schema(type: 'string'), description: 'New password', required: true)]
    #[Route('/api/register', methods:['POST'], name: 'api_security_register')]
    public function register(Request $request): JsonResponse
    {
        // check if register is enabled
        if ($_ENV['REGISTER_ENABLED'] != 'true') {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_FORBIDDEN,
                'message' => 'New registration is disabled by server admin'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        // get data from request
        $username = $request->get('username');
        $password = $request->get('password');

        // get user ip
        $ipAddress = $this->visitorInfoUtil->getIP();

        // check required data
        if ($username == null || $password == null) {
            $errorMessage = null;
            if ($username == null) {
                $errorMessage = 'input username is required';
            }
            if ($password == null) {
                $errorMessage = 'input password is required';
            }
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => $errorMessage
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check username length
        if (strlen($username) < $_ENV['MIN_USERNAME_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'username must be at least ' . $_ENV['MIN_USERNAME_LENGTH'] . ' characters long'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (strlen($username) > $_ENV['MAX_USERNAME_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'username must be maximal ' . $_ENV['MAX_USERNAME_LENGTH'] . ' characters long'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check password length
        if (strlen($password) < $_ENV['MIN_PASSWORD_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'password must be at least ' . $_ENV['MIN_PASSWORD_LENGTH'] . ' characters long'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (strlen($password) > $_ENV['MAX_PASSWORD_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'password must be maximal ' . $_ENV['MAX_PASSWORD_LENGTH'] . ' characters long'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // check if user is exist
        if ($this->userManager->getUserRepo($username) != null) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_CONFLICT,
                'message' => 'user: ' . $username . ' is already exist'
            ], JsonResponse::HTTP_CONFLICT);
        }

        // check if IP is registred
        if ($_ENV['ONE_USER_PER_IP'] == 'true') {
            if ($this->userManager->getUserRepoByIP($ipAddress) != null) {
                return $this->json([
                    'status' => 'error',
                    'code' => JsonResponse::HTTP_CONFLICT,
                    'message' => 'Your ip address is already registred in the system'
                ], JsonResponse::HTTP_CONFLICT);
            }
        }

        try {
            // execute register
            $this->userManager->registerUser($username, $password);

            // return success message
            return $this->json([
                'status' => 'success',
                'code' => JsonResponse::HTTP_OK,
                'message' => 'Registration success'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception) {
            return $this->json([
                'status' => 'error',
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Unexpected register error'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
