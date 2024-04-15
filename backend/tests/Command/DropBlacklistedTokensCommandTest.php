<?php

namespace App\Tests\Command;

use App\Manager\AuthTokenManager;
use App\Command\DropBlacklistedTokensCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Class DropBlacklistedTokensCommandTest
 * 
 * Unit test for the DropBlacklistedTokensCommand.
 * 
 * @package App\Tests\Command
 */
class DropBlacklistedTokensCommandTest extends KernelTestCase
{
    /**
     * Test the execute method of the DropBlacklistedTokensCommand class.
     *
     * This test verifies that the command successfully deletes all blacklisted JWT tokens.
     */
    public function testExecuteDropBlacklistedTokensCommand(): void
    {
        // boot the Symfony kernel
        self::bootKernel();

        // create a mock of AuthTokenManager
        $authTokenManager = $this->createMock(AuthTokenManager::class);

        // configure the mock to expect a call to truncateBlacklistedTokens method
        $authTokenManager->expects($this->once())->method('truncateBlacklistedTokens');

        // create an instance of the command with the mock
        $command = new DropBlacklistedTokensCommand($authTokenManager);

        // create an application instance
        $application = new Application(self::$kernel);
        $application->add($command);

        // create a command tester
        $commandTester = new CommandTester($command);

        // execute the command
        $commandTester->execute([]);

        // get the command output
        $output = $commandTester->getDisplay();

        // check if the output contains the success message
        $this->assertStringContainsString('all blacklist tokens is deleted!', $output);
    }
}
