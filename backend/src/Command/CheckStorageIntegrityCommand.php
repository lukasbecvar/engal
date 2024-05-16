<?php

namespace App\Command;

use App\Manager\LogManager;
use App\Manager\StorageManager;
use App\Repository\MediaRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckStorageIntegrityCommand
 *
 * Command check if storage matching database.
 *
 * @package App\Command
 */
#[AsCommand(name: 'app:check:storage:integrity', description: 'Check database & storage integrity')]
class CheckStorageIntegrityCommand extends Command
{
    private LogManager $logManager;
    private StorageManager $storageManager;
    private MediaRepository $mediaRepository;

    public function __construct(LogManager $logManager, StorageManager $storageManager, MediaRepository $mediaRepository)
    {
        $this->logManager = $logManager;
        $this->storageManager = $storageManager;
        $this->mediaRepository = $mediaRepository;
        parent::__construct();
    }

    /**
     * Executes the command check if storage matching database.
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     *
     * @return int The status code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            // error found state
            $error = false;

            // print info title
            $io->title('Checking storage data integrity');

            // get all media database & storage list
            $mediaFiles = $this->storageManager->getAllMediaFiles();
            $mediaDatabase = $this->mediaRepository->findAllMedia();

            // check database with storage data
            foreach ($mediaDatabase as $media) {
                // print status
                $io->text('checking ' . $media['token'] . ' media file integrity');

                // check file found
                if ($this->storageManager->isMediaExist($media['owner_id'], $media['token'], false) == false) {
                    $error = true;
                    $io->error('error: ' . $media['token'] . ' data file not found');
                }
            }

            // check media storage with database
            foreach ($mediaFiles as $media) {
                // print status
                $io->text('checking ' . $media['token'] . ' media entity integrity');

                // check file found
                if ($this->storageManager->getMediaEntityRepository(['token' => $media['token'], 'owner_id' => $media['user_id']]) == null) {
                    $error = true;
                    $io->error('error: ' . $media['token'] . ' data entity not found');
                }
            }

            // check if error in process
            if ($error) {
                // log success check
                $this->logManager->log('integrity-check', 'integrity check exited with error status');

                $io->error('Integrity error found');
                return Command::FAILURE;
            }

            // log success check
            $this->logManager->log('integrity-check', 'integrity check finished with success status');

            // return success output
            $io->success('Integrity check success');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('error to check integrity: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
