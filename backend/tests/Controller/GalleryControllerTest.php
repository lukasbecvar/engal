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
    private object $client;

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
     * Test retrieving the list of galleries with authentication.
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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

    /**
     * Test for retrieving gallery data with empty name.
     *
     * @return void
     */
    public function testGetGalleryDataEmptyName(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/data');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('gallery_name parameters is required', $responseData['message']);
    }

    /**
     * Test for retrieving gallery data with non-existing gallery name.
     *
     * @return void
     */
    public function testGetGalleryDataNotFound(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/data', [
            'gallery_name' => 'asdfwfwfew_fwfewfwf'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertSame(404, $responseData['code']);
        $this->assertEquals('gallery: asdfwfwfew_fwfewfwf not found in database', $responseData['message']);
    }

    /**
     * Test for retrieving gallery data with existing gallery name.
     *
     * @return void
     */
    public function testGetGalleryDataSuccess(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/data', [
            'gallery_name' => 'testing gallery'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertIsArray($responseData['gallery_data']);
    }

    /**
     * Test for retrieving gallery data without authentication.
     *
     * @return void
     */
    public function testGetGalleryDataNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/data');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }
}
