<?php

namespace App\Tests\Auth;

use Symfony\Component\String\ByteString;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RegisterTest
 *
 * @package App\Tests\Auth
 */
class RegisterTest extends WebTestCase
{
    /**
     * Instance for making requests.
     */
    private KernelBrowser $client;

    /**
     * Set up before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test registration with an empty password.
     *
     * @return void
     */
    public function testRegisterEmptyPassword(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(8)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('input password is required', $responseData['message']);
    }

    /**
     * Test registration with a username shorter than the minimum length.
     *
     * @return void
     */
    public function testRegisterUsernameShort(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom($_ENV['MIN_USERNAME_LENGTH'] - 1)->toString(),
            'password' => ByteString::fromRandom(8)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('username must be at least ' . $_ENV['MIN_USERNAME_LENGTH'] . ' characters long', $responseData['message']);
    }

    /**
     * Test registration with a username longer than the maximu length.
     *
     * @return void
     */
    public function testRegisterUsernameLong(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom($_ENV['MAX_USERNAME_LENGTH'] + 1)->toString(),
            'password' => ByteString::fromRandom(8)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('username must be maximal ' . $_ENV['MAX_USERNAME_LENGTH'] . ' characters long', $responseData['message']);
    }

    /**
     * Test registration with a password shorter than the minimum length.
     *
     * @return void
     */
    public function testRegisterPasswordShort(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(5)->toString(),
            'password' => ByteString::fromRandom($_ENV['MIN_PASSWORD_LENGTH'] - 1)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('password must be at least ' . $_ENV['MIN_PASSWORD_LENGTH'] . ' characters long', $responseData['message']);
    }

    /**
     * Test registration with a password longer than the maximum length.
     *
     * @return void
     */
    public function testRegisterPasswordLong(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(5)->toString(),
            'password' => ByteString::fromRandom($_ENV['MAX_PASSWORD_LENGTH'] + 1)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('password must be maximal ' . $_ENV['MAX_PASSWORD_LENGTH'] . ' characters long', $responseData['message']);
    }

    /**
     * Test registration with a username that already exists.
     *
     * @return void
     */
    public function testRegisterUsernameAlreadyExist(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => 'test',
            'password' => ByteString::fromRandom(10)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_CONFLICT);
        $this->assertSame(409, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('user: test is already exist', $responseData['message']);
    }

    /**
     * Test successful user registration.
     *
     * @return void
     */
    public function testRegisterSuccess(): void
    {
        $this->client->request('POST', '/api/register', [
            'username' => ByteString::fromRandom(10)->toString(),
            'password' => ByteString::fromRandom(10)->toString()
        ]);

        // decode response content
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check reponse
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertSame('success', $responseData['status']);
        $this->assertSame('Registration success', $responseData['message']);
    }
}
