<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MediaControllerTest
 *
 * Unit test case for the MediaController class
 *
 * @package App\Tests\Controller
 */
class MediaControllerTest extends CustomCase
{
    /**
     * Instance for making requests
     */
    private KernelBrowser $client;

    private string $mediaToken;

    /**
     * Set up before each test
     *
     * @return void
    */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        // ensure test user exists
        $user = $this->ensureTestUser();

        // create media for tests
        $this->mediaToken = $this->createTestMedia($user);

        parent::setUp();
    }

    /**
     * Test case for when auth_token & media_token parameters are missing
     *
     * @return void
     */
    public function testGetMediaContentEmptyAuthTokenToken(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }

    /**
     * Test case for when media_token parameter is missing
     *
     * @return void
     */
    public function testGetMediaContentEmptyMediaToken(): void
    {
        $token = $this->loginAndGetToken($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('auth token & media_token parameter is required', $responseData['message']);
    }

    /**
     * Test case for when an incorrect auth_token is provided
     *
     * @return void
     */
    public function testGetMediaContentInvalidAuthToken(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'media_token' => $this->mediaToken,
        ], [], [
            'HTTP_AUTHORIZATION' => 'Bearer invalid_token'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('Invalid JWT Token', $responseData['message']);
    }

    /**
     * Test case for when media_token is not found
     *
     * @return void
     */
    public function testGetMediaContentNotFound(): void
    {
        $token = $this->loginAndGetToken($this->client);

        $this->client->request('GET', '/api/media/content', [
            'media_token' => 'wrong_token',
        ], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertSame(404, $responseData['code']);
        $this->assertEquals('media token: wrong_token not found', $responseData['message']);
    }

    /**
     * Test case for successful retrieval of media content
     *
     * @return void
     */
    public function testGetMediaContentSuccess(): void
    {
        $token = $this->loginAndGetToken($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'media_token' => $this->mediaToken,
        ]);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    /**
     * Test case for when media_token parameter is missing
     *
     * @return void
     */
    public function testGetMediaInfoEmptyToken(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/info');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('media_token parameter is required', $responseData['message']);
    }

    /**
     * Test case for when media_token is not found
     *
     * @return void
     */
    public function testGetMediaInfoWrongToken(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/info', [
            'media_token' => 'wrong_token'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertSame(404, $responseData['code']);
        $this->assertEquals('media token: wrong_token not found', $responseData['message']);
    }

    /**
     * Test case for successful retrieval of media info
     *
     * @return void
     */
    public function testGetMediaInfoSuccess(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/info', [
            'media_token' => $this->mediaToken
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertIsArray($responseData['media_info']);
    }

    /**
     * Test case for when user is not authenticated
     *
     * @return void
     */
    public function testGetMediaInfoNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/info');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }
}
