<?php

namespace App\Tests\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class LogoutTest
 *
 * This class contains unit tests for the logout functionality.
 *
 * @package App\Tests\Auth
 */
class LogoutTest extends WebTestCase
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
     * Test logout with valid JWT token.
     *
     * This test authenticates a testing user and obtains a JWT token.
     * It then sends a logout request with the obtained token and asserts that the logout is successful.
     */
    public function testLogoutWithValidToken(): void
    {
        // authenticate testing user and get JWT token
        $this->client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'test',
                'password' => 'test',
            ])
        );

        $response = $this->client->getResponse();
        $response_data = json_decode($response->getContent(), true);

        // check if login was successful
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertArrayHasKey('token', $response_data);

        $token = $response_data['token'];

        // make logout request with JWT token
        $this->client->request('POST', '/api/logout', [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        $logout_response = $this->client->getResponse();
        $logout_response_data = json_decode($logout_response->getContent(), true);

        // check if logout was successful
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $logout_response_data['code']);
        $this->assertArrayHasKey('status', $logout_response_data);
        $this->assertSame('success', $logout_response_data['status']);
        $this->assertSame('Logout successful', $logout_response_data['message']);
    }

    /**
     * Test logout with invalid JWT token.
     *
     * This test sends a logout request with an invalid JWT token and asserts that the logout fails due to the invalid token.
     */
    public function testLogoutWithInvalidToken(): void
    {
        // make logout request with invalid JWT token
        $this->client->request('POST', '/api/logout', [], [], ['HTTP_AUTHORIZATION' => 'Bearer invalid_token']);

        $logout_response = $this->client->getResponse();
        $logout_response_data = json_decode($logout_response->getContent(), true);

        // check if logout failed due to invalid token
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $logout_response_data['code']);
        $this->assertSame('Invalid JWT Token', $logout_response_data['message']);
    }
}
