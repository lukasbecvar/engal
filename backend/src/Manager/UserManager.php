<?php

namespace App\Manager;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;

/**
 * Class UserManager
 * @package App\Manager
 */
class UserManager
{
    /**
     * @var LogManager $logManager The log manager.
     */
    private LogManager $logManager;

    /**
     * @var ErrorManager $errorManager The error manager.
     */
    private ErrorManager $errorManager;

    /**
     * @var SecurityUtil $securityUtil The security utility.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var VisitorInfoUtil $visitorInfoUtil The visitor info utility.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * @var EntityManagerInterface $entityManager The entity manager.
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * UserManager constructor.
     * @param LogManager $logManager The log manager.
     * @param ErrorManager $errorManager The error manager.
     * @param SecurityUtil $securityUtil The security utility.
     * @param VisitorInfoUtil $visitorInfoUtil The visitor info utility.
     * @param EntityManagerInterface $entityManager The entity manager.
     */
    public function __construct(
        LogManager $logManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil, 
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager    
    ) {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Inserts a new user into the database.
     *
     * @param string $username The username.
     * @param string $password The user's password.
     */
    public function insertNewUser(string $username, string $password): void
    {
        // generate password hash
        $password_hash = $this->securityUtil->genBcryptHash($password, 10);

        // generate user token
        $token = ByteString::fromRandom(32)->toString();

        // get current time
        $time = date('d.m.Y H:i:s');
                
        // default base64 image
        $image_base64 = '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBw4RDQ0OEA0QDhANDQ0NDw4NDhsNDg0OFREWFxcTFRUYICggGBolGxMTITEhJSkrLi4uFx8zODMsNygtLisBCgoKDQ0NDg0NDisZFRkrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrK//AABEIAOYA2wMBIgACEQEDEQH/xAAaAAEAAwEBAQAAAAAAAAAAAAAAAQQFAwIH/8QAMhABAQABAQYEBAQGAwAAAAAAAAECEQMEEiFRkSIxQWEFcYGhQnKxwSMyUoLh8DNi0f/EABUBAQEAAAAAAAAAAAAAAAAAAAAB/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8A+qAKgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIebtMf6p3B7HObXH+qd49ygkQkAAAAAAAAAAAAAAAAAAAEWgjLKSa26SKe232/hn1v/jhvG3uV9vSfu5A9Z7TK+eVv1eNEiiNHrHKzytnyqAFnZb5lPPxT7r2y2kyxlmul6shY3Ta2Zaa8ulvJBpCEgAAAAAAAAAAAAAAAAK2/bSTCzXnfT10WMrpLb6c/oyNpncsrlfX7QHkBQAAAAdN2kueOt05uYDZSr7nteLDn5zlVhAAAAAAAAAAAAAAAABX37LTC+9mP+9mau/EbywnvapAAKAAAAAALPw/LxWdcf0aLL3O/wATH31n2aiAAAAAAAAAAAAAAADjvW14cdZ53lAVfiF8WP5f3VXrabS5XW3V5UAAAAAAAAdN3v8AEw/NGqxpdLrPTmv7nvFytmXPSayoLYAAAAAAAAAAAAACp8Qnhntl+y28bXCZY2X1BkD1tMLjdLNHlQAAAAAAAAWdwnjvtjVaRpbnseHHn53z9vZB3SAAAAAAAAAAAAACEgK2/wD/AB/3Ys5o7/PB/dGcAAoAAAAAAtfD74svy/u0FD4dj4sr6Sad19BCQAAAAAAAAAAAAAABz281wyn/AFrJbNjHzx0tnS6AgBQAAAAAkBf+Hzw29clpz3fDhwxl8/V1QAAAAAAAAAAAAAAAAFLf9l5ZSeXnp0XUWAxha2+52S2XWTW6XlZFVQAAAAWNy2VuUvpOf1eNhsLneknnWls8JjJJ5T7+6D0kAAAAAAAAAAAAAAQCRFrxdrjPxTuDoOGW94T8Wvyjllv2Ppjb9gd95vgy+TKd9tvWWUs0klcFAAAAF74deWU95+i4ydhtrjrppz6rOO/T1x7VBdFeb5h1s+ce8dvhfxQHUeZlOsv1egAAAAAAAAAU983jTwzz9b09gdNvvWOPL+a9J6fNT2m9Z3109pycQC29UaJFAAAAAAAAAAAAB0w2+c8sr8rzjmAvbHfZeWU0955f4W5WMsbrvHDdL/Lfsg0hCQAAAAc9vtOHG325fNk2+t875rvxDK+HGS9byU+G9L2BAnhvS9jhvS9lECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYF/cNrrjcb54/otMzdLcc5yvPleXVpoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=';

        // get user ip
        $ip_address = $this->visitorInfoUtil->getIP();

        // init user object
        $user = new User();

        // set user data
        $user->setUsername($username);
        $user->setPassword($password_hash);
        $user->setToken($token);
        $user->setRole('User');
        $user->setRegisterTime($time);
        $user->setLastLoginTime($time);
        $user->setProfileImage($image_base64);
        $user->setIpAddress($ip_address);

        // try to insert user entity to database
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush(); 

            // log action
            $this->logManager->log('authenticator', 'new user registration, user: '.$username);

        } catch (\Exception $e) {
            $this->errorManager->handleError('Error to flush user entity: '.$e->getMessage(), 500);
        }
    }

    /**
     * Checks if a user can log in with the given credentials.
     *
     * @param string $username The username.
     * @param string $password The user's password.
     * @return bool True if the user can log in, false otherwise.
     */
    public function canLogin(string $username, string $password): bool
    {
        // get user repository 
        $repository = $this->getUserRepository(['username' => $username]);

        // check if user exist
        if ($repository == null) {

            // log action
            $this->logManager->log('authenticator', 'user: '.$username.' trying to login: username not registred, '.$username.':'.$password);

            return false;
        } else {
            // get user password hash
            $password_hash = $repository->getPassword();

            // check if password is valid
            if ($this->securityUtil->hashValidate($password, $password_hash)) {
                
                // get user token
                $token = $this->getUserToken($username);

                // update user data
                $this->updateUserData($token);
                
                // log action
                $this->logManager->log('authenticator', 'user: '.$username.' login successful');

                return true;
            } else { 
                // invalid password

                // log action
                $this->logManager->log('authenticator', 'user: '.$username.' trying to login: wrong password, '.$username.':'.$password);

                return false;
            }
        }
    }

    /**
     * Updates user data such as last login time and IP address.
     *
     * @param string $token The user token.
     */
    public function updateUserData(string $token): void 
    {
        // get date & time
        $date = date('d.m.Y H:i:s');

        // get current visitor ip address
        $ip_address = $this->visitorInfoUtil->getIP();

        // get user data
        $user = $this->getUserRepository(['token' => $token]);

        // check if user repo found
        if ($user != null) {

            // update last login time
            $user->setLastLoginTime($date);

            // update user ip
            $user->setIpAddress($ip_address);

            // update user data
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('Update user data error: '.$e->getMessage(), 500);
            }
        }     
    }

    /**
     * Gets the user repository for a given set of conditions.
     *
     * @param array $array The conditions to search for.
     * @return User|null The user entity or null if not found.
     */
    public function getUserRepository(array $array): ?object 
    {
        $result = null;
        $userRepository = $this->entityManager->getRepository(User::class);

        // try to find user in database
        try {
            $result = $userRepository->findOneBy($array);
        } catch (\Exception $e) {
            $this->errorManager->handleError('Find user entity error: '.$e->getMessage(), 500);
        }

        // return result
        if ($result !== null) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Logs a user out and records the action.
     *
     * @param string $token The user token.
     */
    public function logLogout($token): void
    {
        // get username
        $username = $this->getUserRepository(['token' => $token])->getUsername();

        // log action
        $this->logManager->log('authenticator', 'user: '.$username.' logout successful');
    }

    /**
     * Gets the user token for a given username.
     *
     * @param string $username The username.
     * @return string|null The user token or null if not found.
     */
    public function getUserToken(string $username): ?string
    {
        return $this->getUserRepository(['username' => $username])->getToken();
    }

    /**
     * Gets the username for a given user token.
     *
     * @param string $token The user token.
     * @return string|null The username or null if not found.
     */
    public function getUsernameByToken(string $token): ?string
    {
        return $this->getUserRepository(['token' => $token])->getUsername();
    }
}
