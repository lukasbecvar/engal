<?php

namespace App\Tests\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginTest
 * 
 * This class contains unit tests for the login functionality.
 * 
 * @package App\Tests\Security
 */
class LoginTest extends WebTestCase
{
    /**
     * Test successful login with valid credentials.
     *
     * This test checks if a user can successfully log in with valid credentials.
     * It sends a POST request to the login endpoint with valid username and password.
     * Then, it asserts that the response status code is HTTP_OK (200),
     * the response content is not empty, and it contains a 'token' key in the response data.
     */
    public function testLoginValid(): void
    {
        $client = static::createClient();

        // make request
        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"username": "test", "password": "test"}'
        );

        // get response data
        $response_content = $client->getResponse()->getContent();
        $response_data = json_decode($response_content, true);

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertNotEmpty($response_content);
        $this->assertArrayHasKey('token', $response_data);
        $this->assertNotEmpty($response_data['token']);
    }

    /**
     * Test login failure with invalid credentials.
     *
     * This test checks if a user cannot log in with invalid credentials.
     * It sends a POST request to the login endpoint with invalid username and password.
     * Then, it asserts that the response status code is HTTP_UNAUTHORIZED (401)
     * and the response contains the expected error message 'Invalid credentials.'
     */
    public function testLoginInvalid(): void
    {
        $client = static::createClient();

        // make request
        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"username": "invalid_ěěěščě", "password": "invalid_ěěěščě"}'
        );

        // get response data
        $response_content = $client->getResponse()->getContent();
        $response_data = json_decode($response_content, true);

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertSame('Invalid credentials.', $response_data['message']);
    }
}
