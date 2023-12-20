<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ImageContentTest
 * @package App\Tests
 */
class ImageContentTest extends WebTestCase
{
    /**
     * @var mixed
     */
    private $client;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    /**
     * Test the behavior of the '/image/content' endpoint with an empty token.
     */
    public function testImageContentEmptyToken(): void
    {
        // make post request
        $this->client->request('POST', '/image/content');
        
        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();
        
        // decode JSON content
        $data = json_decode($content, true);
        
        // test response code
        $this->assertResponseStatusCodeSame(200);
        
        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: token');
    }

    /**
     * Test the behavior of the '/image/content' endpoint with an empty gallery.
     */
    public function testImageContentEmptyGallery(): void
    {
        // make post request
        $this->client->request('POST', '/image/content', [
            'token' => 'invalid-token'
        ]);
        
        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();
        
        // decode JSON content
        $data = json_decode($content, true);
        
        // test response code
        $this->assertResponseStatusCodeSame(200);
        
        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: gallery (gallery name)');
    }

    /**
     * Test the behavior of the '/image/content' endpoint with an empty image.
     */
    public function testImageContentEmptyImage(): void
    {
        // make post request
        $this->client->request('POST', '/image/content', [
            'token' => 'invalid-token',
            'gallery' => 'idk'
        ]);
        
        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();
        
        // decode JSON content
        $data = json_decode($content, true);
        
        // test response code
        $this->assertResponseStatusCodeSame(200);
        
        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'required post data: image (image name)');
    }
}
