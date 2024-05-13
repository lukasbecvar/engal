<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures
 *
 * Fixture class for creating a default user.
 *
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    private Connection $connection;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(Connection $connection, UserPasswordHasherInterface $passwordHasher)
    {
        $this->connection = $connection;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Loads data fixtures into the database.
     *
     * @param ObjectManager $manager The object manager.
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // init db transaction
        $this->connection->beginTransaction();

        // create a user
        $user = new User();
        $user->setUsername('test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));
        $user->setRegisterTime(date('d.m.Y H:i:s'));
        $user->setLastLoginTime('not-logged');
        $user->setIpAddress('unknown');

        // save test user
        $manager->persist($user);
        $manager->flush();

        // commit and re-start new transaction
        $this->connection->commit();
        $this->connection->beginTransaction();
    }
}
