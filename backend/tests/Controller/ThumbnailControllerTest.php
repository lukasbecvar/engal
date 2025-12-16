<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ThumbnailControllerTest
 *
 * Unit test case for the ThumbnailController class
 *
 * @package App\Tests\Controller
 */
class ThumbnailControllerTest extends CustomCase
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

        $user = $this->ensureTestUser();
        $this->mediaToken = $this->createTestMedia($user);
        parent::setUp();
    }

    /**
     * Test case for retrieving media thumbnail when the token parameter is empty
     *
     * @return void
     */
    public function testGetMediaThumbnailEmptyToken(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/thumbnail');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('token parameter is required', $responseData['message']);
    }

    /**
     * Test case for retrieving media thumbnail with a wrong token parameter
     *
     * @return void
     */
    public function testGetMediaThumbnailWrongToken(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/thumbnail', [
            'width' => 200,
            'height' => 200,
            'token' => 'wrong-token'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertSame(404, $responseData['code']);
        $this->assertEquals('media token: wrong-token not found', $responseData['message']);
    }

    /**
     * Test case for successfully retrieving media thumbnail with valid parameters
     *
     * @return void
     */
    public function testGetMediaThumbnaiSuccess(): void
    {
        $token = $this->loginAndGetToken($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/thumbnail', [
            'width' => 200,
            'height' => 200,
            'token' => $this->mediaToken
        ], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token
        ]);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    /**
     * Test case for retrieving media thumbnail without authentication
     *
     * @return void
     */
    public function testGetMediaThumbnaiNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/thumbnail');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }

    /**
     * Test for retrieving media thumbnails preload without authentication
     *
     * @return void
     */
    public function testGetMediaThumbnailsPreloadNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/thumbnail/preload');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }
}
