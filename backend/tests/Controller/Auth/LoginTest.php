<?php

namespace App\Tests\Auth;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginTest
 *
 * This class contains unit tests for the login functionality.
 *
 * @package App\Tests\Auth
 */
class LoginTest extends WebTestCase
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
     * Test successful login with valid credentials.
     *
     * This test checks if a user can successfully log in with valid credentials.
     * It sends a POST request to the login endpoint with valid username and password.
     * Then, it asserts that the response status code is HTTP_OK (200),
     * the response content is not empty, and it contains a 'token' key in the response data.
     *
     * @return void
     */
    public function testLoginValid(): void
    {
        // make request
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username": "test", "password": "test"}'
        );

        // get response data
        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertNotEmpty($responseContent);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEmpty($responseData['token']);
    }

    /**
     * Test login failure with invalid credentials.
     *
     * This test checks if a user cannot log in with invalid credentials.
     * It sends a POST request to the login endpoint with invalid username and password.
     * Then, it asserts that the response status code is HTTP_UNAUTHORIZED (401)
     * and the response contains the expected error message 'Invalid credentials.'
     *
     * @return void
     */
    public function testLoginInvalid(): void
    {
        // make request
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username": "invalid_ěěěščě", "password": "invalid_ěěěščě"}'
        );

        // get response data
        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertSame('Invalid credentials.', $responseData['message']);
    }
}
