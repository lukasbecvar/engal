<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SecurityUtilTest
 * @package App\Tests\Util
 */
class SecurityUtilTest extends TestCase
{
    /**
     * @var SecurityUtil
     */
    private $securityUtil;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->securityUtil = new SecurityUtil();
    }

    /**
     * Test the escapeString method to ensure proper string escaping.
     */
    public function testEscapeString(): void
    {
        $input = '<script>alert("XSS");</script>';
        $expected = '&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;';
        $this->assertEquals($expected, $this->securityUtil->escapeString($input));
    }

    /**
     * Test the hashValidate method to validate password hashing and verification.
     */
    public function testHashValidate(): void
    {
        $plainTextPassword = 'password123';
        $hash = password_hash($plainTextPassword, PASSWORD_BCRYPT);
        $this->assertTrue($this->securityUtil->hashValidate($plainTextPassword, $hash));
        $this->assertFalse($this->securityUtil->hashValidate('wrongPassword', $hash));
    }

    /**
     * Test the genBcryptHash method to generate and validate bcrypt hash.
     */
    public function testGenBcryptHash(): void
    {
        $plainTextPassword = 'password123';
        $cost = 12;
        $hash = $this->securityUtil->genBcryptHash($plainTextPassword, $cost);
        $this->assertTrue(password_verify($plainTextPassword, $hash));
    }
}
