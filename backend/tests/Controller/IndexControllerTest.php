<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
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
     *
     * @return void
     */
    public function testInitIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        // decode output data
        $responseData = json_decode($client->getResponse()->getContent(), true);

        // check response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertSame(200, $responseData['code']);

        // check status response
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);

        // check response code
        $this->assertArrayHasKey('code', $responseData);

        // check backend_version
        $this->assertArrayHasKey('backend_version', $responseData);
        $this->assertArrayHasKey('backend_version', $responseData);
        $this->assertIsString($responseData['security_policy']['REGISTER_ENABLED']);
        $this->assertIsInt($responseData['security_policy']['MIN_USERNAME_LENGTH']);
        $this->assertIsInt($responseData['security_policy']['MAX_USERNAME_LENGTH']);
        $this->assertIsInt($responseData['security_policy']['MIN_PASSWORD_LENGTH']);
        $this->assertIsInt($responseData['security_policy']['MAX_PASSWORD_LENGTH']);

        // check message
        $this->assertSame('Engal API is loaded success', $responseData['message']);
    }
}
