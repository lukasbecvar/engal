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
    /** @var SecurityUtil */
    private $securityUtil;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->securityUtil = new SecurityUtil();
    }

    /**
     * Test escaping a string for safe use in HTML content.
     */
    public function testEscapeString()
    {
        $input = '<script>alert("XSS");</script>';
        $expectedOutput = '&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;';

        $result = $this->securityUtil->escapeString($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * Test validating a plain text password against its hashed version.
     */
    public function testHashValidation()
    {
        $plain_text_password = 'mySecretPassword';
        $hash = password_hash($plain_text_password, PASSWORD_BCRYPT);

        $result = $this->securityUtil->hashValidate($plain_text_password, $hash);

        $this->assertTrue($result);
    }

    /**
     * Test generating a bcrypt hash for the given plain text password.
     */
    public function testGenBcryptHash()
    {
        $plain_text_password = 'mySecretPassword';
        $cost = 12;

        $result = $this->securityUtil->genBcryptHash($plain_text_password, $cost);

        $this->assertNotNull($result);
        $this->assertTrue(password_verify($plain_text_password, $result));
    }

    /**
     * Test encrypting and decrypting text using the AES-256-CBC algorithm.
     */
    public function testEncryptAndDecryptAES()
    {
        $plain_text = 'SensitiveData123';

        $encrypted = $this->securityUtil->encryptAES($plain_text);
        $decrypted = $this->securityUtil->decryptAES($encrypted);

        $this->assertNotEquals($plain_text, $encrypted);
        $this->assertEquals($plain_text, $decrypted);
    }
}
