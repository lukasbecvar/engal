<?php

namespace App\Tests\Util;

use App\Util\SiteUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SiteUtilTest
 * @package App\Tests\Util
 */
class SiteUtilTest extends TestCase
{
    /**
     * @var SiteUtil
     */
    private $siteUtil;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->siteUtil = new SiteUtil();
    }

    /**
     * Test the getHttpHost method to ensure proper retrieval of HTTP host.
     */
    public function testGetHttpHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertEquals('example.com', $this->siteUtil->getHttpHost());
    }

    /**
     * Test the isMaintenance method to check if the application is in maintenance mode.
     */
    public function testIsMaintenance(): void
    {
        $_ENV['MAINTENANCE_MODE'] = 'true';
        $this->assertTrue($this->siteUtil->isMaintenance());

        $_ENV['MAINTENANCE_MODE'] = 'false';
        $this->assertFalse($this->siteUtil->isMaintenance());
    }

    /**
     * Test the isDevMode method to check if the application is in dev mode.
     */
    public function testIsDevMode(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue($this->siteUtil->isDevMode());

        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse($this->siteUtil->isDevMode());
    }

    /**
     * Test the isRegisterEnabled method to check if user registrations are enabled.
     */
    public function testIsRegisterEnabled(): void
    {
        $_ENV['REGISTRATIONS'] = 'true';
        $this->assertTrue($this->siteUtil->isRegisterEnabled());

        $_ENV['REGISTRATIONS'] = 'false';
        $this->assertFalse($this->siteUtil->isRegisterEnabled());
    }

    /**
     * Test the isRunningLocalhost method to check if the application is running on localhost.
     */
    public function testIsRunningLocalhost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->assertTrue($this->siteUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $this->assertTrue($this->siteUtil->isRunningLocalhost());

        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertFalse($this->siteUtil->isRunningLocalhost());
    }

    /**
     * Test the isSsl method to check if the connection is over SSL.
     */
    public function testIsSsl(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->siteUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->siteUtil->isSsl());
    }
}
