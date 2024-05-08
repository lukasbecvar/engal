<?php

namespace App\Tests\Controller\User;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UserStatusControllerTest
 * 
 * Unit test case for the UserStatusController class.
 * 
 * @package App\Tests\Controller\User
 */
class UserStatusControllerTest extends CustomCase
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
     * Tests the getUserStatus endpoint.
     *
     * This method tests the behavior of the getUserStatus endpoint by sending a GET request
     * and asserting the response status code and content.
     *
     * @return void
     */
    public function testGetUserStatus(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/user/status');

        // decoding the content of the JsonResponse
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $response_data['code']);
        $this->assertEquals('success', $response_data['status']);
        $this->assertEquals('test', $response_data['username']);
        $this->assertEquals(['ROLE_USER'], $response_data['roles']);
    }

    /**
     * Test retrieving user status when the user is not authenticated.
     */
    public function testGetUserStatusNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/user/status');

        // decoding the content of the JsonResponse
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $response_data['code']);
        $this->assertEquals('JWT Token not found', $response_data['message']);
    }
}
