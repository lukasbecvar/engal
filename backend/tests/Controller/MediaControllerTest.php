<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class MediaControllerTest extends CustomCase
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

    public function testGetMediaContentEmptyToken(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('token parameter is required', $responseData['message']);
    }

    public function testGetMediaContentWrongToken(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'token' => 'wrong-token'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertSame(404, $responseData['code']);
        $this->assertEquals('media token: wrong-token not found', $responseData['message']);
    }

    public function testGetMediaContentSuccess(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'token' => '853bc196bb6bdf5f72c33e1eeeb8a8e2'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    public function testGetMediaContentNonAuth(): void
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
}
