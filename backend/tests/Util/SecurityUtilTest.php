<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SecurityUtilTest
 *
 * Test class for SecurityUtil
 *
 * @package App\Tests\Util
 */
class SecurityUtilTest extends TestCase
{
    private SecurityUtil $securityUtil;

    protected function setUp(): void
    {
        $this->securityUtil = new SecurityUtil();
        parent::setUp();
    }

    /**
     * Test method escapeString()
     *
     * Tests whether the escapeString() method correctly escapes special characters
     * in a string to prevent HTML injection
     *
     * @return void
     */
    public function testEscapeString(): void
    {
        // escape XSS
        $actual = $this->securityUtil->escapeString('<script>alert("Hello!");</script>');

        // assert return
        $this->assertEquals('&lt;script&gt;alert(&quot;Hello!&quot;);&lt;/script&gt;', $actual);
    }
}
