<?php

namespace App\Manager;

use App\Entity\User;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserManager
 *
 * Manages user-related operations such as updating user data on login.
 *
 * @package App\Manager
 */
class UserManager
{
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasherInterface;

    /**
     * UserManager constructor.
     *
     * @param ErrorManager $errorManager The error manager
     * @param VisitorInfoUtil $visitorInfoUtil The visitor info utility
     * @param EntityManagerInterface $entityManager The entity manager
     * @param UserPasswordHasherInterface $passwordHasherInterface The password hasher utility
     */
    public function __construct(
        ErrorManager $errorManager, 
        VisitorInfoUtil $visitorInfoUtil, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasherInterface
    ) {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->passwordHasherInterface = $passwordHasherInterface;
    }

    /**
     * Updates user data on login.
     *
     * Finds the user by username and updates the last login time and IP address.
     *
     * @param string $identifier The username or identifier of the user
     * 
     * @return void
     * 
     * @throws \Exception If there is an error while updating user data
     */
    public function updateUserDataOnLogin(string $identifier): void
    {
        // get user repo
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $identifier]);

        // check if user is found
        if ($user) {
            try {
                // set new data
                $user->setLastLoginTime(date('d.m.Y H:i:s'));
                $user->setIpAddress($this->visitorInfoUtil->getIP());

                // flush user data
                $this->entityManager->flush();

            } catch (\Exception $e) {
                $this->errorManager->handleError('error to update user data with login: '.$e->getMessage(), 500);
            }
        }
    }

    /**
     * Checks if a user exists in the database.
     *
     * @param string $username The username to check
     * 
     * @return bool True if the user exists, false otherwise
     */
    public function isUserExist(string $username): bool
    {
        // get user repo
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        // check if user found in database
        if ($user != null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Registers a new user.
     *
     * @param string $username The username of the new user
     * @param string $password The password of the new user
     * 
     * @return void
     * 
     * @throws \Exception If there is an error while registering the user
     */
    public function registerUser(string $username, string $password): void
    {
        // check if user exist
        if (!$this->isUserExist($username)) {
            
            try {
                // init user entity
                $user = new User();

                // hash password
                $password = $this->passwordHasherInterface->hashPassword($user, $password);

                // set user property
                $user->setUsername($username);
                $user->setPassword($password);
                $user->setRoles(['ROLE_USER']);
                $user->setRegisterTime(date('d.m.Y H:i:s'));
                $user->setLastLoginTime('non-logged');
                $user->setIpAddress('non-logged');

                // flush user to database
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to register new user: '.$e->getMessage(), 500);
            }
        }
    }
}
