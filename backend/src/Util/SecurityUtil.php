<?php

namespace App\Util;

class SecurityUtil
{
    public function escapeString(string $string): ?string 
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }
}
