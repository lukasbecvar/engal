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
     * Encrypts data using AES encryption.
     *
     * Encrypts the provided data using AES-256-CBC encryption algorithm if storage encryption is enabled.
     * If encryption is not enabled, the data remains unchanged.
     *
     * @param mixed $data The data to be encrypted.
     *
     * @return mixed The encrypted data, or the original data if encryption is not enabled.
     */
    public function encryptAES(mixed $data): mixed
    {
        // check if encryption is enabled
        if ($_ENV['STORAGE_ENCRYPTION'] != 'true') {
            return $data;
        }

        // get config values
        $key = $_ENV['STORAGE_ENCRYPTION_KEY'];
        $iv = $_ENV['ENCRYPTION_VECTOR'];

        // hash the encryption key
        $key = hash('sha256', $key, true);

        // encrypt the data
        $cipherText = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);

        // return encrypted data
        return $iv . $cipherText;
    }

    /**
     * Decrypts data encrypted using AES encryption.
     *
     * Decrypts the provided data encrypted using AES-256-CBC encryption algorithm if storage encryption is enabled.
     * If encryption is not enabled, the data remains unchanged.
     *
     * @param mixed $data The data to be decrypted.
     *
     * @return mixed The decrypted data, or the original data if encryption is not enabled or decryption fails.
     */
    public function decryptAES(mixed $data): mixed
    {
        // check if encryption is enabled
        if ($_ENV['STORAGE_ENCRYPTION'] != 'true') {
            return $data;
        }

        // get config values
        $key = $_ENV['STORAGE_ENCRYPTION_KEY'];
        $iv = $_ENV['ENCRYPTION_VECTOR'];

        // hash the encryption key
        $key = hash('sha256', $key, true);

        // decrypt the data
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');

        // extract the iv and cipher text
        $iv = str_pad($iv, $ivLength, "\0");

        // decrypt the data
        $cipherText = substr($data, $ivLength);

        // decrypt the data
        return openssl_decrypt($cipherText, 'aes-256-cbc', $key, 0, $iv);
    }
}
