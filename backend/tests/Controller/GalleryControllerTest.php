<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\Response;

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
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('success', $response_data['status']);
        $this->assertEquals(200, $response_data['code']);
        $this->assertIsArray($response_data['gallery_names']);
    }

    /**
     * Test retrieving the list of galleries without authentication.
     */
    public function testGetGalleryListNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/gallery/list');

        // decoding the content of the JsonResponse
        $response_data = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('JWT Token not found', $response_data['message']);
        $this->assertEquals(401, $response_data['code']);
    }
}
