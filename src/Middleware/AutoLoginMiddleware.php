<?php

namespace App\Middleware;

use App\Entity\User;
use App\Util\CookieUtil;
use App\Helper\LoginHelper;
use App\Helper\EntityHelper;

/*
    This middleware check if requird autologin function
*/

class AutoLoginMiddleware
{
    
    private $loginHelper;
    private $entityHelper;

    public function __construct(EntityHelper $entityHelper, LoginHelper $loginHelper) 
    {
        $this->loginHelper = $loginHelper;
        $this->entityHelper = $entityHelper;
    }

    public function onKernelRequest(): void
    {
        // check if cookie set
        if (isset($_COOKIE['login-token-cookie'])) {
            
            $user = new User();
            $user_token = $_COOKIE['login-token-cookie'];

            // check if token exist in database
            if ($this->entityHelper->isEntityExist(['token' => $user_token], $user)) {
                
                // get user data
                $user = $this->entityHelper->getEntityValue(['token' => $user_token], $user);

                // autologin user
                $this->loginHelper->login($user->getUsername(), $user_token, true);
            } else {
                CookieUtil::unset('login-token-cookie');
        
                // start session (for destroy xDDDDD)
                $this->loginHelper->startSession();
                // destroy all sessions
                session_destroy();
            }
        }
    }
}
