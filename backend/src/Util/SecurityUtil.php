<?php

namespace App\Util;

/**
 * Class SecurityUtil
 *
 * Utility class for security-related operations.
 *
 * @package App\Util
 */
class SecurityUtil
{
    /**
     * Escapes a string to prevent XSS attacks.
     *
     * @param string $string The string to escape.
     *
     * @return string|null The escaped string.
     */
    public function escapeString(string $string): ?string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Encrypts the given data using AES-256-CBC algorithm.
     *
     * @param mixed $data The data to be encrypted.
     *
     * @return mixed The encrypted data, base64 encoded, with the initialization vector prepended.
     *               Returns the original data if encryption is not enabled.
     */
    public function encryptAES(mixed $data): mixed
    {
        // check if encryption is enabled
        if ($_ENV['STORAGE_ENCRYPTION'] !== 'true') {
            return $data;
        }

        // generate encryption key
        $key = hash('sha256', $_ENV['STORAGE_ENCRYPTION_KEY'], true);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // encrypt data
        $cipherText = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);

        // return encrypted data
        return base64_encode($iv . $cipherText);
    }

    /**
     * Decrypts the given data using AES-256-CBC algorithm.
     *
     * @param mixed $data The base64 encoded encrypted data with the initialization vector prepended.
     *
     * @return mixed The decrypted data. Returns the original data if encryption is not enabled.
     */
    public function decryptAES(mixed $data): mixed
    {
        // check if encryption is enabled
        if ($_ENV['STORAGE_ENCRYPTION'] !== 'true') {
            return $data;
        }

        // generate encryption key
        $key = hash('sha256', $_ENV['STORAGE_ENCRYPTION_KEY'], true);
        $data = base64_decode($data);

        // decrypt data
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $cipherText = substr($data, $ivLength);

        // return decrypted data
        return openssl_decrypt($cipherText, 'aes-256-cbc', $key, 0, $iv);
    }
}
