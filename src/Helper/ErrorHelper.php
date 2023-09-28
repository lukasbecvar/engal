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

    public function handleErrorView(string $code)
    {
        // try to get view
        try {
            $view = $this->twig->render('errors/error-'.$code.'.html.twig');
        } catch (\Exception) {
            $view = $this->twig->render('errors/error-unknown.html.twig');
        }

        // die app & render error view
        die($view);
    }

    public function handleError(string $msg, int $code)
    {
        // check if app in devmode
        if ($_ENV['APP_ENV'] == 'dev') {

            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => $msg
            ];

            // kill app & send error json
            die(json_encode($data));

        // error (for non devmode visitors)
        } else {
            die($this->handleErrorView($code));
        }
    }
}
