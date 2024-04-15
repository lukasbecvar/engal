<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserStatusControllerTest
 * 
 * Unit test case for the UserStatusController class.
 * 
 * @package App\Tests\Controller
 */
class UserStatusControllerTest extends CustomCase
{
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
        $client = static::createClient();

        // simulate user authentication
        $this->simulateUserAuthentication($client);

        // GET request to the API endpoint
        $client->request('GET', '/api/user/status');

        // decoding the content of the JsonResponse
        $response_data = json_decode($client->getResponse()->getContent(), true);

        // asserts
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('success', $response_data['status']);
        $this->assertEquals(200, $response_data['code']);
        $this->assertEquals('test', $response_data['username']);
        $this->assertEquals(['ROLE_USER'], $response_data['roles']);
    }

    /**
     * Test retrieving user status when the user is not authenticated.
     */
    public function testGetUserStatusNonAuth(): void
    {
        $client = static::createClient();

        // GET request to the API endpoint
        $client->request('GET', '/api/user/status');

        // decoding the content of the JsonResponse
        $response_data = json_decode($client->getResponse()->getContent(), true);

        // asserts
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('JWT Token not found', $response_data['message']);
        $this->assertEquals(401, $response_data['code']);
    }
}
