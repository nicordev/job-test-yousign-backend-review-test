<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImportGitHubEvents\ImportGitHubEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
class ImportGitHubEventsCommand extends Command
{
    protected static $defaultName = 'app:import-github-events';

    public function __construct(private ImportGitHubEvents $importGitHubEvents)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events')
            ->addArgument('query', InputArgument::REQUIRED, 'Enter the requested date slot with this format YYYY-MM-DD-h (for instance: 2024-01-20-16)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = $input->getArgument('query');
        $this->importGitHubEvents->setQuery($query);
        $this->importGitHubEvents->execute();

        $output->writeln("Events of $query successfully imported.");

        return Command::SUCCESS;
    }
}
