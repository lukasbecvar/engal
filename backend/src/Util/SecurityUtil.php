<?php

namespace App\Util;

/**
 * Class SecurityUtil
 * @package App\Util
 */
class SecurityUtil
{
    /**
     * Escapes a string for safe use in HTML content.
     *
     * @param string $string The string to escape.
     * @return string|null The escaped string.
     */
    public function escapeString(string $string): ?string 
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * Validates a plain text password against its hashed version.
     *
     * @param string $plain_text The plain text password.
     * @param string $hash The hashed password.
     * @return bool True if the validation is successful, false otherwise.
     */
    public function hashValidate(string $plain_text, string $hash): bool 
	{
		return password_verify($plain_text, $hash);
	}

    /**
     * Generates a bcrypt hash for the given plain text password.
     *
     * @param string $plain_text The plain text password.
     * @param int $cost The cost parameter for bcrypt.
     * @return string The generated bcrypt hash.
     */
	public function genBcryptHash(string $plain_text, int $cost): string 
	{
		return password_hash($plain_text, PASSWORD_BCRYPT, ['cost' => $cost]);
	}

    /**
     * Encrypts the given text using the AES-256-CBC algorithm.
     *
     * @param string $plain_text The input text to be encrypted.
     * @return string Encrypted text in Base64 format.
     */
    public function encryptAES(string $plain_text): string
    {
        $key = $_ENV['APP_SECRET'];
        $iv = $_ENV['ENCRYPTION_VECTOR'];
        $key = hash('sha256', $key, true);
        $cipher_text = openssl_encrypt($plain_text, 'aes-256-cbc', $key, 0, $iv);
        $encrypted = base64_encode($iv . $cipher_text);
        return $encrypted;
    }

    /**
     * Decrypts the given text that was encrypted using the AES-256-CBC algorithm.
     *
     * @param string $encrypted Encrypted text in Base64 format.
     * @return string Decrypted text.
     */
    public function decryptAES(string $encrypted): string
    {
        $key = $_ENV['APP_SECRET'];
        $iv = $_ENV['ENCRYPTION_VECTOR'];
        $key = hash('sha256', $key, true); 
        $data = base64_decode($encrypted);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = str_pad($iv, $iv_length, "\0");
        $cipher_text = substr($data, $iv_length);
        $plain_text = openssl_decrypt($cipher_text, 'aes-256-cbc', $key, 0, $iv);
        return $plain_text;
    }
}
