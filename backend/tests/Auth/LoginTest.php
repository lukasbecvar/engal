<?php

namespace App\Tests\Auth;

use App\Entity\User;
use Symfony\Component\String\ByteString;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginTest
 * @package App\Tests\Auth
 */
class LoginTest extends WebTestCase
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
        $fakeUser = $userRepository->findOneBy(['username' => 'test_username']);
    
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
     * Test for login with an empty username.
     */
    public function testLoginEmptyUsername(): void
    {
        // make post request
        $this->client->request('POST', '/login');
        
        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();
        
        // decode JSON content
        $data = json_decode($content, true);
        
        // test response code
        $this->assertResponseStatusCodeSame(200);
        
        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: username');
    }

    /**
     * Test for login with an empty password.
     */
    public function testLoginEmptyPassword(): void
    {
        // make post request
        $this->client->request('POST', '/login', [
            'username' => ByteString::fromRandom(12)->toString()
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
        $this->assertSame($data['message'], 'required post data: password');
    }

    /**
     * Test for login with incorrect data.
     */
    public function testLoginIncorectData(): void
    {
        // make post request
        $this->client->request('POST', '/login', [
            'username' => ByteString::fromRandom(12)->toString(),
            'password' => ByteString::fromRandom(12)->toString()
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
        $this->assertSame($data['message'], 'incorrect username or password');
    }

    /**
     * Test for a valid login.
     */
    public function testLoginValid(): void
    {
        // make post request
        $this->client->request('POST', '/login', [
            'username' => 'test_username',
            'password' => 'test_password'
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
        $this->assertSame($data['message'], 'login with username: test_username successfully');
        $this->assertSame($data['token'], 'zbjNNyuudM3HQGWe6xqWwjyncbtZB22D');
    }
}
