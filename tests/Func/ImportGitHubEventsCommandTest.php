<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ImportGitHubEventsCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    public function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-github-events');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteIsSuccessful(): void
    {
        $this->commandTester->execute([
            'query' => '2015-01-01-0',
        ]);

        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Events of 2015-01-01-0 successfully imported.', $output);
    }

    public function testExecuteWithWrongQuery(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-github-events');
        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute([
            'query' => '2015-01-01',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Query must follow format YYYY-MM-DD-h.', $output);
    }
}
