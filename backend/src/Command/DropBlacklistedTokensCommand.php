<?php

namespace App\Command;

use App\Manager\AuthTokenManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DropBlacklistedTokensCommand
 * 
 * Console command to delete all blacklisted JWT tokens.
 * 
 * @package App\Command
 */
#[AsCommand(name: 'app:drop-blacklisted-tokens', description: 'Delete all blacklisted JWT tokens')]
class DropBlacklistedTokensCommand extends Command
{
    private AuthTokenManager $authTokenManager;

    /**
     * DropBlacklistedTokensCommand constructor.
     *
     * @param AuthTokenManager $authTokenManager The authentication token manager
     */
    public function __construct(AuthTokenManager $authTokenManager)
    {   
        $this->authTokenManager = $authTokenManager;
        parent::__construct();
    }

    /**
     * Executes the console command to delete all blacklisted JWT tokens.
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     * 
     * @return int Returns 0 if command executed successfully, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->authTokenManager->truncateBlacklistedTokens();
            $io->success('all blacklist tokens is deleted!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('error to truncate blacklisted tokens table: '.$e->getMessage());
            return Command::FAILURE;
        }
    }
}
