<?php

namespace App\Tests\Command;

use App\Tests\CustomCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Class GrantAdminRoleCommandTest
 *
 * Unit test for the GrantAdminRoleCommandTest
 *
 * @package App\Tests\Command
 */
class GrantAdminRoleCommandTest extends CustomCase
{
    /**
     * Test the execution of the "app:grant-admin" command
     *
     * @return void
     */
    public function testExecuteAdminRoleCommand(): void
    {
        // boot the Symfony kernel
        self::bootKernel();

        // ensure baseline user
        $this->ensureTestUser();

        // create an application instance
        $application = new Application(self::$kernel);

        // get the command by its name
        $command = $application->find('app:grant-admin');

        // create a command tester
        $commandTester = new CommandTester($command);

        // execute the command with arguments
        $commandTester->execute([
            'username' => 'test',
        ]);

        // get output
        $output = $commandTester->getDisplay();

        // check output
        $this->assertStringContainsString('admin role granted to: test', $output);
    }
}
