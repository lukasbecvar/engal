<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\ByteString;

/**
 * Class RegisterTest
 * 
 * @package App\Tests\Security
 */
class RegisterTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser Instance for making requests.
     */
    private $client;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test registration with an empty password.
     */
    public function testRegisterEmptyPassword(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(8)->toString()
        ]);

        // decode response content
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertSame('error', $response_data['status']);
        $this->assertSame('input password is required', $response_data['message']);
    }

    /**
     * Test registration with a username shorter than the minimum length.
     */
    public function testRegisterUsernameShort(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom($_ENV['MIN_USERNAME_LENGTH']-1)->toString(),
            'password' => ByteString::fromRandom(8)->toString()
        ]);

        // decode response content
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertSame('error', $response_data['status']);
        $this->assertSame('username must be at least '.$_ENV['MIN_USERNAME_LENGTH'].' characters long', $response_data['message']);
    }

    /**
     * Test registration with a password shorter than the minimum length.
     */
    public function testRegisterPasswordShort(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(5)->toString(),
            'password' => ByteString::fromRandom($_ENV['MIN_PASSWORD_LENGTH']-1)->toString()
        ]);

        // decode response content
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertSame('error', $response_data['status']);
        $this->assertSame('password must be at least '.$_ENV['MIN_PASSWORD_LENGTH'].' characters long', $response_data['message']);
    }

    /**
     * Test registration with a username that already exists.
     */
    public function testRegisterUsernameAlreadyExist(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => 'test',
            'password' => ByteString::fromRandom(10)->toString()
        ]);

        // decode response content
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        $this->assertSame('error', $response_data['status']);
        $this->assertSame('user: test is already exist', $response_data['message']);
    }

    /**
     * Test successful user registration.
     */
    public function testRegisterSuccess(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(10)->toString(),
            'password' => ByteString::fromRandom(10)->toString()
        ]);

        // decode response content
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('success', $response_data['status']);
        $this->assertSame('Registration success', $response_data['message']);
    }
}
