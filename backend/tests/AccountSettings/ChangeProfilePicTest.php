<?php

namespace App\Tests\AccountSettings;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ChangeProfilePicTest
 * @package App\Tests\AccountSettings
 */
class ChangeProfilePicTest extends WebTestCase
{
    /**
     * @var mixed
     */
    private $client;

    /**
     * Set up test data and environment.
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
     * Tear down test data and environment.
     */
    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

    /**
     * Remove fake user data after test execution.
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
     * Tests the behavior when the token is empty.
     */
    public function testChangePicWithEmptyToken(): void
    {
        // make post request
        $this->client->request('POST', '/account/settings/pic');

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
     * Tests the behavior when the image is empty.
     */
    public function testChangePicWithEmptyImage(): void
    {
        // make post request
        $this->client->request('POST', '/account/settings/pic', [
            'token' => 'testing-token'
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
        $this->assertSame($data['message'], 'required post data: image (base64)');
    }

    /**
     * Tests the behavior when an invalid token is provided.
     */
    public function testChangePicWithInvalidToken(): void
    {
        // make post request
        $this->client->request('POST', '/account/settings/pic', [
            'token' => 'invalid-token',
            'image' => 'image-code'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();
        
        // decode JSON content
        $data = json_decode($content, true);
            
        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 403);
        $this->assertSame($data['message'], 'invalid token value');
    }

    /**
     * Tests the behavior with valid data.
     */
    public function testChangePicWithValidData(): void
    {
        // make post request
        $this->client->request('POST', '/account/settings/pic', [
            'token' => 'zbjNNyuudM3HQGWe6xqWwjyncbtZB22D',
            'image' => 'image-code'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();
        
        // decode JSON content
        $data = json_decode($content, true);
            
        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'success');
        $this->assertSame($data['code'], 200);
        $this->assertSame($data['message'], 'profile image update success');
    }
}
