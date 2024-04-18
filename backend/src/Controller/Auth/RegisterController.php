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
    /**
     * Registers a new user.
     *
     * @param Request $request The request object.
     * @param UserManager $userManager The user manager service.
     * @param VisitorInfoUtil $visitorInfoUtil The visitor information utility.
     * 
     * @return JsonResponse The JSON response.
     */
    #[Tag(name: "Auth")]
    #[Route('/api/register', methods:['POST'], name: 'api_security_register')]
    #[Response(response: 200, description: 'The success user register message')]
    #[Response(response: 400, description: 'Invalid request data message')]
    #[Response(response: 403, description: 'New register is disabled by server admin')]
    #[Response(response: 409, description: 'Username is already exist error')]
    #[Parameter(name: 'username', in: 'query', schema: new Schema(type: 'string'), description: 'New username', required: true)]
    #[Parameter(name: 'password', in: 'query', schema: new Schema(type: 'string'), description: 'New password', required: true)]
    public function register(Request $request, UserManager $userManager, VisitorInfoUtil $visitorInfoUtil): JsonResponse
    {
        // check if register is enabled
        if ($_ENV['REGISTER_ENABLED'] != 'true') {
            return $this->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'New registration is disabled by server admin'
            ], 403);
        }

        // get data from request
        $username = $request->get('username');
        $password = $request->get('password');

        // get user ip
        $ip_address = $visitorInfoUtil->getIP();
            
        // check required data
        if ($username == null || $password == null) {
            $error_message = null;
            if ($username == null) {
                $error_message = 'input username is required';
            }
            if ($password == null) {
                $error_message = 'input password is required';
            }
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => $error_message
            ], 400);
        }

        // check username length
        if (strlen($username) < $_ENV['MIN_USERNAME_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'username must be at least '.$_ENV['MIN_USERNAME_LENGTH'].' characters long'
            ], 400);
        }
        if (strlen($username) > $_ENV['MAX_USERNAME_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'username must be maximal '.$_ENV['MAX_USERNAME_LENGTH'].' characters long'
            ], 400);
        }

        // check password length
        if (strlen($password) < $_ENV['MIN_PASSWORD_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'password must be at least '.$_ENV['MIN_PASSWORD_LENGTH'].' characters long'
            ], 400);
        }
        if (strlen($password) > $_ENV['MAX_PASSWORD_LENGTH']) {
            return $this->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'password must be maximal '.$_ENV['MAX_PASSWORD_LENGTH'].' characters long'
            ], 400);
        }

        // check if user is exist
        if ($userManager->getUserRepo($username) != null) {
            return $this->json([
                'status' => 'error',
                'code' => 409,
                'message' => 'user: '.$username.' is already exist'
            ], 409);
        }

        // check if IP is registred
        if ($_ENV['ONE_USER_PER_IP'] == 'true') {
            if ($userManager->getUserRepoByIP($ip_address) != null) {
                return $this->json([
                    'status' => 'error',
                    'code' => 409,
                    'message' => 'Your ip address is already registred in the system'
                ], 409);
            }    
        }

        // final user register
        try {

            // execute register
            $userManager->registerUser($username, $password);

            // return success message
            return $this->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Registration success'
            ], 200);
        } catch (\Exception) {
            return $this->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Unexpected register error'
            ], 500);
        }
    }
}
