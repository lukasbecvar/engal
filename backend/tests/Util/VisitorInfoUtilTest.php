<?php

namespace App\Tests\Util;

use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class VisitorInfoUtilTest
 * @package App\Tests\Util
 */
class VisitorInfoUtilTest extends TestCase
{
    /**
     * @var VisitorInfoUtil
     */
    private $visitorInfoUtil;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->visitorInfoUtil = new VisitorInfoUtil();
    }

    /**
     * Test the getIP method to ensure proper retrieval of the visitor's IP address.
     */
    public function testGetIP(): void
    {
        $this->assertEquals('127.0.0.1', $this->visitorInfoUtil->getIP());
    }

    /**
     * Test the getBrowser method to ensure proper retrieval of the visitor's browser agent.
     */
    public function testGetBrowser(): void
    {
        $this->assertEquals('test', $this->visitorInfoUtil->getBrowser());
    }
}
