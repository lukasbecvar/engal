<?php

namespace App\Tests;

use App\Entity\User;
use App\Manager\StorageManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class CustomCase
 *
 * @package App\Tests
 */
class CustomCase extends WebTestCase
{
    /**
     * Simulate user authentication for testing purposes.
     *
     * @param object $client The Symfony test client.
     *
     * @return void
     */
    public function simulateUserAuthentication(object $client): void
    {
        // fetching the Symfony Container
        $container = $client->getContainer();
        $user = $this->ensureTestUser();

        $token = new UsernamePasswordToken(
            $user,
            'api',
            $user->getRoles()
        );

        // set the token in the token storage
        $container->get('security.token_storage')->setToken($token);
    }

    /**
     * Creates a fake UploadedFile instance for testing purposes.
     *
     * This method generates a fake temporary file with the given filename and MIME type.
     * It creates an empty file in the system's temporary directory and returns an UploadedFile object
     * representing that file. The file will be considered as uploaded.
     *
     * @param string $filename The name of the fake file.
     * @param string $mimeType The MIME type of the fake file.
     *
     * @return UploadedFile A fake UploadedFile instance representing the fake file.
     */
    public function createFakeUploadedFile(string $filename, string $mimeType, string $content = ''): UploadedFile
    {
        // Generate a temporary file path
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test_file');

        // Create an empty temporary file
        file_put_contents($tempFilePath, $content);

        // Return an UploadedFile instance representing the fake file
        return new UploadedFile($tempFilePath, $filename, $mimeType, null, true);
    }

    /**
     * Generates a JWT token for the given user.
     *
     * @param UserInterface $user The user for whom the token is generated.
     * @return string The generated JWT token.
     */
    public function generateJwtToken(UserInterface $user): string
    {
        // Get JWT token manager
        $jwtManager = self::getContainer()->get(JWTTokenManagerInterface::class);
        // Generate JWT token
        return $jwtManager->create($user);
    }

    /**
     * Ensure a test user exists and return it.
     */
    protected function ensureTestUser(): User
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $user = $userRepository->findOneBy(['username' => 'test']);

        if ($user === null) {
            $user = new User();
            $user->setRegisterTime(date('d.m.Y H:i:s'));
            $user->setLastLoginTime('not-logged');
            $user->setIpAddress('unknown');
        }

        // reset baseline credentials/roles for tests
        $user->setUsername('test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'test'));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Create a test media (image) for a user and return its token.
     */
    protected function createTestMedia(User $user): string
    {
        /** @var StorageManager $storageManager */
        $storageManager = self::getContainer()->get(StorageManager::class);

        // tiny 2x2 png generated via GD to avoid libpng warnings
        $image = imagecreatetruecolor(2, 2);
        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        $uploaded = $this->createFakeUploadedFile('test.png', 'image/png', $pngData);

        $token = $storageManager->storeMediaEntity([
            'name' => 'test.png',
            'gallery_name' => 'testing gallery',
            'type' => 'image/png',
            'length' => '00:00',
            'owner_id' => (string) $user->getId(),
            'upload_time' => date('d.m.Y H:i:s'),
        ]);

        if ($token === null) {
            $this->fail('Failed to create media entity for test');
        }

        $storageManager->storeMediaFile($token, $uploaded, $user->getId(), 'photos');

        return $token;
    }

    /**
     * Login and get auth token
     *
     * @param KernelBrowser $client The client for making requests
     *
     * @return string The auth token
     */
    public function loginAndGetToken(KernelBrowser $client): string
    {
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username": "test", "password": "test"}'
        );

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $token = null;
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'auth_token') {
                $token = $cookie->getValue();
                break;
            }
        }

        $this->assertNotNull($token);
        $client->getCookieJar()->set(new Cookie('auth_token', $token));

        return (string) $token;
    }
}
