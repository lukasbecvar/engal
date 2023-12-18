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
}
