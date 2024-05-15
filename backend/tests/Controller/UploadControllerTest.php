<?php

namespace App\Tests\Controller;

use App\Tests\CustomCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UploadControllerTest
 *
 * Test media upload component
 *
 * @package App\Tests\Controller
 */
class UploadControllerTest extends CustomCase
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
     * Test retrieving upload policy configuration.
     */
    public function testGetUploadPolicy(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // GET request to the API endpoint
        $this->client->request('GET', '/api/upload/config/policy');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertEquals('success', $responseData['status']);
        $this->assertSame($responseData['policy']['FILE_UPLOAD_STATUS'], $_ENV['FILE_UPLOAD_STATUS']);
        $this->assertSame($responseData['policy']['MAX_FILES_COUNT'], $_ENV['MAX_FILES_COUNT']);
        $this->assertSame($responseData['policy']['MAX_FILES_SIZE'], $_ENV['MAX_FILES_SIZE']);
        $this->assertSame($responseData['policy']['MAX_GALLERY_NAME_LENGTH'], $_ENV['MAX_GALLERY_NAME_LENGTH']);
        $this->assertSame($responseData['policy']['ALLOWED_FILE_EXTENSIONS'], json_decode($_ENV['ALLOWED_FILE_EXTENSIONS'], true));
    }

    /**
     * Test retrieving upload policy configuration without authentication.
     */
    public function testGetUploadPolicyNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('GET', '/api/upload/config/policy');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertSame(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }

    /**
     * Test file upload with an empty gallery name.
     */
    public function testFileUploadEmptyGalleryName(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // make request to the endpoint
        $this->client->request(
            'POST',
            '/api/upload',
        );

        // get response
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('your gallery name is empty', $responseData['message']);
    }

    /**
     * Test file upload with a long gallery name.
     */
    public function testFileUploadLongGalleryName(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // make request to the endpoint
        $this->client->request(
            'POST',
            '/api/upload',
            ['gallery_name' => 'ofkoewfkwofkwofofkfowkfowekfowkfowfkofkewofkewofkwofkfowkfokfoewf'], // gallery name
        );

        // get response
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('maximal gallery name length is ' . $_ENV['MAX_GALLERY_NAME_LENGTH'], $responseData['message']);
    }

    /**
     * Test file upload with empty files.
     */
    public function testFileUploadEmptyFiles(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // make request to the endpoint
        $this->client->request(
            'POST',
            '/api/upload',
            ['gallery_name' => 'test_gallery'], // gallery name
        );

        // get response
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertSame(400, $responseData['code']);
        $this->assertSame('error', $responseData['status']);
        $this->assertSame('your files input is empty', $responseData['message']);
    }

    /**
     * Test successful file upload.
     */
    public function testFileUploadSuccess(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // prepare files for upload
        $files = [
            $this->createFakeUploadedFile('test1.png', 'image/png'),
            $this->createFakeUploadedFile('test2.jpg', 'image/jpg')
        ];

        // make request to the endpoint
        $this->client->request(
            'POST',
            '/api/upload',
            ['gallery_name' => 'test_gallery'], // gallery name
            ['files' => $files] // files to upload
        );

        // get response
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame(200, $responseData['code']);
        $this->assertSame('success', $responseData['status']);
        $this->assertSame('files uploaded successfully', $responseData['message']);
    }

    /**
     * Test file upload without authentication.
     */
    public function testUploadNonAuth(): void
    {
        // GET request to the API endpoint
        $this->client->request('POST', '/api/upload');

        // decoding the content of the JsonResponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
        $this->assertEquals(401, $responseData['code']);
        $this->assertEquals('JWT Token not found', $responseData['message']);
    }
}
