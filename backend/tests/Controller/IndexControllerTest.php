<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class IndexControllerTest
 * 
 * Test main app index/status controller response
 * 
 * @package App\Tests\Controller
 */
class IndexControllerTest extends WebTestCase
{
    /**
     * Test for the index action of IndexController.
     *
     * This test verifies that the index action returns a successful JSON response
     * with the expected structure and data.
     */
    public function testInitIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        // decode output data
        $response_data = json_decode($client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        // check status response
        $this->assertArrayHasKey('status', $response_data);
        $this->assertEquals('success', $response_data['status']);

        // check response code
        $this->assertArrayHasKey('code', $response_data);
        $this->assertEquals(200, $response_data['code']);

        // check backend_version
        $this->assertArrayHasKey('backend_version', $response_data);
        $this->assertArrayHasKey('backend_version', $response_data);
        $this->assertIsString($response_data['security_policy']['REGISTER_ENABLED']);
        $this->assertIsInt($response_data['security_policy']['MIN_USERNAME_LENGTH']);
        $this->assertIsInt($response_data['security_policy']['MAX_USERNAME_LENGTH']);
        $this->assertIsInt($response_data['security_policy']['MIN_PASSWORD_LENGTH']);
        $this->assertIsInt($response_data['security_policy']['MAX_PASSWORD_LENGTH']);

        // check message
        $this->assertSame('Engal API is loaded success', $response_data['message']);
    }
}
