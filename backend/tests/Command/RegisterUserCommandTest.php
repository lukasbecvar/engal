<?php

namespace App\Tests\Command;

use Symfony\Component\String\ByteString;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Class RegisterUserCommandTest
 *
 * Unit test for the RegisterUserCommand.
 *
 * @package App\Tests\Command
 */
class RegisterUserCommandTest extends KernelTestCase
{
    /**
     * Test the execute method of the RegisterUserCommand class.
     *
     * This test verifies that the command successfully registers a new user.
     */
    public function testExecuteRegisterUserCommand(): void
    {
        // boot the Symfony kernel
        self::bootKernel();

        // create an application instance
        $application = new Application(self::$kernel);

        // get the command by its name
        $command = $application->find('app:register-user');

        // create a command tester
        $commandTester = new CommandTester($command);

        // testing username
        $username = ByteString::fromRandom(10)->toString();

        // execute the command with arguments
        $commandTester->execute([
            'username' => $username,
        ]);

        // get output
        $output = $commandTester->getDisplay();

        // check output
        $this->assertStringContainsString('New user registred username: ' . $username, $output);
    }
}
