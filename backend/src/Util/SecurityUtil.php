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
}
