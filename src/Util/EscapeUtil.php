<?php

namespace App\Util;

/*
    EscapeUtil util provides string escape methods
*/

class EscapeUtil
{

    /*
      * The function for replace dangerous chars in string (XSS proteection)
      * Usage like special_chars_strip("<script>alert(xss)</script>")
      * Input string
      * Returned secure string
    */
    public static function special_chars_strip($string): ?string {
        return htmlspecialchars($string, ENT_QUOTES);
    }
}
