<?php

namespace App\Controller;

use App\Manager\LogManager;
use App\Manager\ErrorManager;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;

class SecurityController extends AbstractController
{
    private LogManager $logManager;
    private ErrorManager $errorManager;

    public function __construct(LogManager $logManager, ErrorManager $errorManager) {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
    }

    #[Security(name: "Bearer")]
    #[Route('/api/logout', name: 'app_security_logout', methods: ['POST'])]
    public function logout(SecurityBundleSecurity $security): JsonResponse
    {
        // get user
        $user = $security->getUser();

        try {
            // log logout
            $this->logManager->log('authenticator', 'User '.$user->getUserIdentifier().' logged out');

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
