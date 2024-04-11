<?php

namespace App\Command;

use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GrantAdminRoleCommand
 * 
 * Command to grant admin role to a user.
 *
 * @package App\Command
 */
#[AsCommand(name: 'app:grant-admin', description: 'Grant admin role to user')]
class GrantAdminRoleCommand extends Command
{
    private UserManager $userManager;

    /**
     * GrantAdminRoleCommand constructor.
     *
     * @param UserManager $userManager The user manager
     */
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
        $this->addArgument('username', InputArgument::OPTIONAL, 'New admin username');
    }

    /**
     * Executes the command to grant admin role to a user.
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
            $io->error('You must add the admin username argument!');
            return Command::FAILURE;
        } else {

            // check if username is used
            if ($this->userManager->getUserRepo($username) == null) {
                $io->error('Error username: '.$username.' is not registred!');
                return Command::FAILURE;
            } else {
                try {
                    // check if user is admin
                    if ($this->userManager->isUserAdmin($username)) {
                        $io->error('User: '.$username.' is already admin');
                        return Command::FAILURE;
                    } else {
                        // grant role to user
                        $this->userManager->addAdminRoleToUser($username);
    
                        $io->success('admin role granted to: '.$username);
                        return Command::SUCCESS;
                    }
                } catch (\Exception $e) {
                    $io->success('error to grant admin: '.$e->getMessage());
                    return Command::FAILURE;
                }
            }
        }
    }
}
