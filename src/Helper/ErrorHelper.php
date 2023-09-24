<?php

namespace App\Helper;

use Twig\Environment;

/*
    Error helper provides error handle operations
*/

class ErrorHelper
{

    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    // handle error twig view
    public function handleErrorView($code)
    {
        try {
            $view = $this->twig->render("errors/error-$code.html.twig");
        } catch (\Exception) {
            $view = $this->twig->render("errors/error-unknown.html.twig");
        }

        // die app & render error view
        die($view);
    }

    // handle error msg (if env is prod = render error view)
    public function handleError(string $msg, int $code)
    {
        // check if app in devmode
        if ($_ENV["APP_ENV"] == "dev") {
            die("DEV-MODE: $msg");

        // error (for non devmode visitors)
        } else {
            die($this->handleErrorView($code));
        }
    }
}
