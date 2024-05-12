<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class GalleryControllerTest
 *
 * Test get gallery list
 *
 * @package App\Tests\Controller
 */
class GalleryControllerTest extends CustomCase
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
     * Test retrieving the list of galleries with authentication.
     */
    public function testGetGalleryList(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/list');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertEquals('success', $responseData['status']);
        $this->assertIsArray($responseData['gallery_list']);
    }

    /**
     * Test retrieving the list of galleries without authentication.
     */
    public function testGetGalleryListNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/list');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }

    /**
     * Test for retrieving gallery statistics with user authentication.
     */
    public function testGetGalleryStats(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/stats');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertEquals('success', $responseData['status']);
        $this->assertIsArray($responseData['stats']);
    }

    /**
     * Test for retrieving gallery statistics without user authentication.
     */
    public function testGetGalleryStatsNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/stats');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }
}
