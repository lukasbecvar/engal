<?php

namespace App\Util;

/**
 * Class SecurityUtil
 *
 * Utility class for security-related operations
 *
 * @package App\Util
 */
class SecurityUtil
{
    /**
     * Escapes a string to prevent XSS attacks
     *
     * @param string $string The string to escape
     *
     * @return string|null The escaped string
     */
    public function escapeString(string $string): ?string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Encrypt arbitrary string (deterministic) for storage
     *
     * @param string $data The data to encrypt
     *
     * @return string The encrypted data
     */
    public function encryptName(string $data): string
    {
        if (!$this->isEncryptionEnabled()) {
            return $data;
        }

        $key = $this->getKey();
        if ($key === null) {
            return $data;
        }

        // deterministic IV derived from value (avoids extra columns for lookups)
        $iv = substr(hash('sha256', $data . $key, true), 0, 12);
        $tag = null;

        $cipher = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($cipher === false || !$tag || strlen($tag) !== 16) {
            return $data;
        }

        return base64_encode($iv . $tag . $cipher);
    }

    /**
     * Decrypt string previously encrypted with encryptName (best effort)
     *
     * @param string $data The data to decrypt
     *
     * @return string The decrypted data
     */
    public function decryptName(string $data): string
    {
        if (!$this->isEncryptionEnabled()) {
            return $data;
        }

        $key = $this->getKey();
        if ($key === null) {
            return $data;
        }

        $decoded = base64_decode($data, true);
        if ($decoded === false || strlen($decoded) < 28) {
            return $data; // not encrypted or malformed
        }

        $iv = substr($decoded, 0, 12);
        $tag = substr($decoded, 12, 16);
        $ciphertext = substr($decoded, 28);

        $plain = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        return $plain === false ? $data : $plain;
    }

    /**
     * Legacy aliases kept for backward compatibility
     *
     * @param mixed $data The data to encrypt
     *
     * @return mixed The encrypted data
     */
    public function encryptAES(mixed $data): mixed
    {
        if (!is_string($data)) {
            return $data;
        }

        return $this->encryptName($data);
    }

    /**
     * Legacy aliases kept for backward compatibility
     *
     * @param mixed $data The data to decrypt
     *
     * @return mixed The decrypted data
     */
    public function decryptAES(mixed $data): mixed
    {
        if (!is_string($data)) {
            return $data;
        }

        return $this->decryptName($data);
    }

    /**
     * Check if encryption is enabled
     *
     * @return bool True if encryption is enabled, false otherwise
     */
    private function isEncryptionEnabled(): bool
    {
        return ($_ENV['STORAGE_ENCRYPTION_ENABLED'] ?? 'false') === 'true';
    }

    /**
     * Returns 32-byte key or null if missing
     *
     * @return string|null The 32-byte key
     */
    private function getKey(): ?string
    {
        $rawKey = $_ENV['STORAGE_ENCRYPTION_KEY'] ?? $_ENV['APP_SECRET'] ?? null;

        if (empty($rawKey)) {
            return null;
        }

        return hash('sha256', $rawKey, true);
    }
}
