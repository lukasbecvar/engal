<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @throws \Symfony\Component\Security\Core\Exception\InvalidArgumentException
     */
    public function simulateUserAuthentication(object $client): void
    {
        // fetching the Symfony Container
        $container = $client->getContainer();

        // creating a fake user entity
        $fakeUser = new \App\Entity\User();
        $fakeUser->setUsername('test');

        // encode a password for the user
        $password = $container->get('security.password_hasher')->hashPassword($fakeUser, 'test');
        $fakeUser->setPassword($password);

        $fakeUser->setRoles(['ROLE_USER']);

        // create a token for the fake user
        $token = new UsernamePasswordToken(
            $fakeUser,
            'api',
            $fakeUser->getRoles()
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
     * @return UploadedFile A fake UploadedFile instance representing the fake file.
     */
    public function createFakeUploadedFile(string $filename, string $mimeType): UploadedFile
    {
        // Generate a temporary file path
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test_file');
        
        // Create an empty temporary file
        file_put_contents($tempFilePath, '');
        
        // Return an UploadedFile instance representing the fake file
        return new UploadedFile($tempFilePath, $filename, $mimeType, null, true);
    }
}
