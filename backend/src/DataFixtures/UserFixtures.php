<?php

namespace App\DataFixtures;

use App\Entity\User;
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
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher The password hasher interface.
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
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
        // create a user
        $user = new User();
        $user->setUsername('test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));

        // save test user
        $manager->persist($user);
        $manager->flush();
    }
}