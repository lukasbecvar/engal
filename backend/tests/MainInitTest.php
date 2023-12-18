<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MainInitTest
 * @package App\Tests
 */
class MainInitTest extends WebTestCase
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
     * Test the main initialization by making a GET request to the root URL.
     */
    public function testMain(): void
    {
        // make get request
        $this->client->request('GET', '/');

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'success');
        $this->assertSame($data['code'], 200);
        $this->assertSame($data['message'], 'Engal services loaded successfully');
    }
}
