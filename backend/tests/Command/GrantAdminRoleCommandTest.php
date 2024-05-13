<?php

namespace App\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class GrantAdminRoleCommandTest extends KernelTestCase
{
    public function testExecuteAdminRoleCommand(): void
    {
        // boot the Symfony kernel
        self::bootKernel();

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
