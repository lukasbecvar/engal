<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SecurityUtilTest
 *
 * Test class for SecurityUtil.
 *
 * @package App\Tests\Util
 */
class SecurityUtilTest extends TestCase
{
    private SecurityUtil $securityUtil;

    /**
     * Set up before each test.
     *
     * @return void
    */
    protected function setUp(): void
    {
        $this->securityUtil = new SecurityUtil();
        parent::setUp();
    }

    /**
     * Test method escapeString().
     *
     * Tests whether the escapeString() method correctly escapes special characters
     * in a string to prevent HTML injection.
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

    /**
     * Tests the encryption and decryption functionality.
     *
     * This method performs the following steps:
     * 1. Encrypts a test string 'test' using the encryptAES method.
     * 2. Decrypts the encrypted string using the decryptAES method.
     * 3. Asserts that the decrypted string matches the original test string.
     *
     * @return void
     */
    public function testEncryption(): void
    {
        $encrypted = $this->securityUtil->encryptAES('test');
        $decrypted = $this->securityUtil->decryptAES($encrypted);

        $this->assertEquals('test', $decrypted);
    }
}
