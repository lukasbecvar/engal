<?php

namespace App\Util;

class SecurityUtil
{
    public function escapeString(string $string): ?string 
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    public function hashValidate(string $plain_text, string $hash): bool 
	{
		return password_verify($plain_text, $hash);
	}

	public function genBcryptHash(string $plain_text, int $cost): string 
	{
		return password_hash($plain_text, PASSWORD_BCRYPT, ['cost' => $cost]);
	}
}
