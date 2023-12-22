<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UploadTest
 * @package App\Tests
 */
class UploadTest extends WebTestCase
{
    /**
     * @var mixed
     */
    private $client;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();

        // initialize and create a fake user
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        
        // get user repository
        $userRepository = $entityManager->getRepository(\App\Entity\User::class);
        $existingUser = $userRepository->findOneBy(['username' => 'test_username']);
    
        // check if user exist
        if (!$existingUser) {

            // create a new User entity
            $user = new User();
            $user->setUsername('test_username');
            $user->setPassword(password_hash('test_password', PASSWORD_BCRYPT));
            $user->setToken('zbjNNyuudM3HQGWe6xqWwjyncbtZB22D');
            $user->setRole('User');
            $user->setRegisterTime('20.11.2023 14:13:06');
            $user->setLastLoginTime('20.11.2023 14:13:06');
            $user->setProfileImage('image');
            $user->setIpAddress('127.0.0.1');
    
            // persist and flush new user to the database
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }

    /**
     * Clean up the test environment.
     */
    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

    /**
     * Remove fake data created during the test.
     */
    private function removeFakeData(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);
        $fakeUser = $userRepository->findOneBy(['token' => 'zbjNNyuudM3HQGWe6xqWwjyncbtZB22D']);
    
        // check if user exist
        if ($fakeUser) {
            $id = $fakeUser->getId();
    
            $entityManager->remove($fakeUser);
            $entityManager->flush();
    
            // reset auto-increment
            $connection = $entityManager->getConnection();
            $connection->executeStatement('ALTER TABLE users AUTO_INCREMENT = ' . ($id - 1));
        }
    }

    /**
     * Test uploading media with an empty token.
     */
    public function testUploadEmptyToken(): void
    {
        // make request
        $this->client->request('POST', '/media/upload');

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: token');
    }

    /**
     * Test uploading media with an empty gallery.
     */
    public function testUploadEmptyGallery(): void
    {
        // make request
        $this->client->request('POST', '/media/upload', [
            'token' => 'zbjNNyuudM3HQGWe6xqWwjyncbtZB22D'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: gallery');
    }

    /**
     * Test uploading media with an empty image file.
     */
    public function testUploadEmptyMedia(): void
    {
        // make request
        $this->client->request('POST', '/media/upload', [
            'token' => 'zbjNNyuudM3HQGWe6xqWwjyncbtZB22D',
            'gallery' => 'testing-gallery'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: image file');
    }
}
