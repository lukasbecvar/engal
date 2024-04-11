<?php

namespace App\Manager;

use App\Entity\User;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

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

    /**
     * UserManager constructor.
     *
     * @param ErrorManager $errorManager The error manager
     * @param VisitorInfoUtil $visitorInfoUtil The visitor info utility
     * @param EntityManagerInterface $entityManager The entity manager
     */
    public function __construct(ErrorManager $errorManager, VisitorInfoUtil $visitorInfoUtil, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
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
}
