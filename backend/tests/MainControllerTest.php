<?php

namespace App\Tests\Public;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MainControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    public function testMainController()
    {
        // make get request
        $this->client->request('GET', '/');

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // test response data
        $this->assertSame($data['status'], 'success');
        $this->assertSame($data['code'], 200);
        $this->assertSame($data['message'], 'Engal services loaded successfully');
    }
}
