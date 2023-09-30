<?php

namespace App\Helper;

use App\Entity\User;
use App\Util\CookieUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Login helper provides all login/logout methods
*/

class LoginHelper
{

    private $logHelper;
    private $errorHelper;
    private $entityHelper;
    private $entityManager;

    public function __construct(
        LogHelper $logHelper, 
        ErrorHelper $errorHelper, 
        EntityHelper $entityHelper, 
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->entityHelper = $entityHelper;
        $this->entityManager = $entityManager;
    }

    public function startSession(): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setLastLoginDate() {

        // get current date
        $date = date('d.m.Y H:i:s');

        // user repository
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user) {

            // update values
            $user->setLastLogin($date);

            // try to flush updated data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
            }
        }     
    }

    public function getUserToken(): string {

        // default token value
        $token = null;

        // init user entity
        $userEntity = new User();

        // start session
        $this->startSession();

        // check if session exist
        if (isset($_SESSION['login-token'])) {

            // check if token exist in database
            if ($this->entityHelper->isEntityExist(['token' => $_SESSION['login-token']], $userEntity)) {
                $token = $_SESSION['login-token'];
            }
        }

        return $token;
    }

    public function isUserLogedin(): bool {

        // default state
        $state = false;

        // init user entity
        $userEntity = new User();

        // start session
        $this->startSession();

        // check if session exist
        if (isset($_SESSION['login-token'])) {

            // check if token exist in database
            if ($this->entityHelper->isEntityExist(['token' => $_SESSION['login-token']], $userEntity)) {
                $state = true;
            }
        }

        return $state;
    }

    public function getUsername(): string {

        $username = null;

        // user repository
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $this->getUserToken()]);

        // check if user repo found
        if ($user) {
            $username = $user->getUsername();
        } 

        return $username;
    }

    public function login(string $username, string $userToken, bool $remember): void {

        // start session
        $this->startSession();

        // check if user token is valid
        if (!empty($userToken)) {
            $_SESSION['login-token'] = $userToken;

            // check if remember set
            if ($remember) {
                CookieUtil::set("login-token-cookie", $userToken, time() + (60*60*24*7*365));
            }

            // update last login time
            $this->setLastLoginDate();

            // log to mysql
            $this->logHelper->log('authenticator', 'user: '.$username.' logged in');

        } else {
            $this->errorHelper->handleError('error to login user with token: '.$userToken, 500);
        }
    }

    public function logout(): void {
        
        $this->startSession();

        // init user entity
        $userEntity = new User();
        $user = $this->entityHelper->getEntityValue(['token' => $this->getUserToken()], $userEntity);

        // unset user-token cookie
        CookieUtil::unset("login-token-cookie");

        // log action to mysql
        $this->logHelper->log('authenticator', 'user: '.$user->getUsername().' logout');

        // destroy all sessions
        session_destroy();
    }
}
