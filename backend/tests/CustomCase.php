<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class CustomCase
 * 
 * @package App\Tests
 */
class CustomCase extends WebTestCase
{
    /**
     * Simulate user authentication for testing purposes.
     *
     * @param object $client The Symfony test client.
     * @throws \Symfony\Component\Security\Core\Exception\InvalidArgumentException
     */
    public function simulateUserAuthentication(object $client): void
    {
        // fetching the Symfony Container
        $container = $client->getContainer();

        // creating a fake user entity
        $fakeUser = new \App\Entity\User();
        $fakeUser->setUsername('test');

        // encode a password for the user
        $password = $container->get('security.password_hasher')->hashPassword($fakeUser, 'test');
        $fakeUser->setPassword($password);

        $fakeUser->setRoles(['ROLE_USER']);

        // create a token for the fake user
        $token = new UsernamePasswordToken(
            $fakeUser,
            'api',
            $fakeUser->getRoles()
        );

        // set the token in the token storage
        $container->get('security.token_storage')->setToken($token);
    }
}
