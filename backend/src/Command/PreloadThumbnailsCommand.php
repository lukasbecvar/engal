<?php

namespace App\Command;

use App\Manager\ThumbnailManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PreloadThumbnailsCommand
 *
 * Command to preload app media thumbnails.
 *
 * @package App\Command
 */
#[AsCommand(name: 'app:thumbnails:preload', description: 'Preload all media thumbnails files')]
class PreloadThumbnailsCommand extends Command
{
    private ThumbnailManager $thumbnailManager;

    public function __construct(ThumbnailManager $thumbnailManager)
    {
        $this->thumbnailManager = $thumbnailManager;
        parent::__construct();
    }

    /**
     * Executes the command to preload thumbnails list.
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
            $io->success('Thumbnails preload processing...');

            // run preload process
            $this->thumbnailManager->preloadAllThumbnails('console_command');

            // return success output
            $io->success('Thumbnails preload success');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('error to preload thumbnails: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
