<?php

namespace App\Tests\Auth;

use App\Tests\CustomCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class LogoutTest
 *
 * This class contains unit tests for the logout functionality
 *
 * @package App\Tests\Auth
 */
class LogoutTest extends CustomCase
{
    /**
     * Instance for making requests
     */
    private KernelBrowser $client;

    /**
     * Set up before each test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->ensureTestUser();
        parent::setUp();
    }

    /**
     * Test logout with valid JWT token
     *
     * This test authenticates a testing user and obtains a JWT token
     * It then sends a logout request with the obtained token and asserts that the logout is successful
     *
     * @return void
     */
    public function testLogoutWithValidToken(): void
    {
        // authenticate testing user and get JWT token
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'test',
                'password' => 'test',
            ])
        );

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // check if login was successful
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame('success', $responseData['status']);

        // make logout request; auth token is stored in cookie
        $this->client->request('POST', '/api/logout');

        $logoutResponse = $this->client->getResponse();
        $logoutResponseData = json_decode($logoutResponse->getContent(), true);

        // check if logout was successful
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $logoutResponseData['code']);
        $this->assertArrayHasKey('status', $logoutResponseData);
        $this->assertSame('success', $logoutResponseData['status']);
        $this->assertSame('Logout successful', $logoutResponseData['message']);
    }

    /**
     * Test logout with invalid JWT token
     *
     * This test sends a logout request with an invalid JWT token and asserts that the logout fails due to the invalid token
     *
     * @return void
     */
    public function testLogoutWithInvalidToken(): void
    {
        // make logout request with invalid JWT token
        $this->client->request('POST', '/api/logout', [], [], ['HTTP_AUTHORIZATION' => 'Bearer invalid_token']);

        $logoutResponse = $this->client->getResponse();
        $logoutResponseData = json_decode($logoutResponse->getContent(), true);

        // check if logout failed due to invalid token
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $logoutResponseData['code']);
        $this->assertSame('Invalid JWT Token', $logoutResponseData['message']);
    }
}
