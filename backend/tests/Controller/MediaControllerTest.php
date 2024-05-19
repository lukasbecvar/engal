<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use App\Repository\UserRepository;
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
     * @var string testing user token
    */
    private string $jwtToken;

    /**
     * Set up before each test.
     *
     * @return void
    */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Get user from test database (assuming you have UserRepository)
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->find(1); // Assuming user ID is 1

        // Generate JWT token for the user
        $this->jwtToken = $this->generateJwtToken($user);

        parent::setUp();
    }

    /**
     * Test case for when auth_token & media_token parameters are missing.
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
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('auth_token & media_token parameter is required', $responseData['message']);
    }

    /**
     * Test case for when media_token parameter is missing.
     *
     * @return void
     */
    public function testGetMediaContentEmptyMediaToken(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'auth_token' => 'auth'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertEquals('auth_token & media_token parameter is required', $responseData['message']);
    }

    /**
     * Test case for when an incorrect auth_token is provided.
     *
     * @return void
     */
    public function testGetMediaContentWrongAuthToken(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'auth_token' => $this->jwtToken,
            'media_token' => 'wrong_token',
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
        $this->assertSame(404, $responseData['code']);
        $this->assertEquals('media token: wrong_token not found', $responseData['message']);
    }

    /**
     * Test case for successful retrieval of media content.
     *
     * @return void
     */
    public function testGetMediaContentSuccess(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/content', [
            'auth_token' => $this->jwtToken,
            'media_token' => '853bc196bb6bdf5f72c33e1eeeb8a8e2',
        ]);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

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

    public function testGetMediaInfoSuccess(): void
    {
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/media/info', [
            'media_token' => '853bc196bb6bdf5f72c33e1eeeb8a8e2'
        ]);

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertIsArray($responseData['media_info']);
    }

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
