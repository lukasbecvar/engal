<?php

namespace App\Command;

use App\Manager\UserManager;
use Symfony\Component\String\ByteString;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RegisterUserCommand
 * 
 * Command to register a new user.
 *
 * @package App\Command
 */
#[AsCommand(name: 'app:register-user', description: 'Register new user')]
class RegisterUserCommand extends Command
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::OPTIONAL, 'New user username');
    }

    /**
     * Executes the command to register a new user.
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     * 
     * @return int The status code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // get username argument
        $username = $input->getArgument('username');

        // check if username is added
        if ($username == null) {
            $io->error('You must add the new username argument!');
            return Command::FAILURE;
        }
    
        // check if username is used
        if ($this->userManager->getUserRepo($username) != null) {
            $io->error('Error username: '.$username.' is already used!');
            return Command::FAILURE;
        }
        
        try {
            // generate user password
            $password = ByteString::fromRandom(32)->toString();

            // register user
            $this->userManager->registerUser($username, $password);

            $io->success('New user registred username: '.$username.' with password: '.$password);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->success('error to register user: '.$e->getMessage());
            return Command::FAILURE;
        }
    }
}
