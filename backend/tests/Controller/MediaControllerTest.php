<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MediaControllerTest
 *
 * Unit test case for the MediaController class.
 *
 * @package App\Tests\Controller
 */
class MediaControllerTest extends CustomCase
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
     * Test case for retrieving media content when the token parameter is empty.
     *
     * @return void
     */
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

    /**
     * Test case for retrieving media content with a wrong token parameter.
     *
     * @return void
     */
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

    /**
     * Test case for successfully retrieving media content with a valid token parameter.
     *
     * @return void
     */
    public function testGetMediaContentSuccess(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'token' => '853bc196bb6bdf5f72c33e1eeeb8a8e2'
        ]);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    /**
     * Test case for retrieving media content without authentication.
     *
     * @return void
     */
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
