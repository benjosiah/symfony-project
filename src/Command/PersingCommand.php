<?php

namespace App\Command;

use App\Service\PersingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\PerseMessage;

#[AsCommand(
    name: 'app:persing',
    description: 'Add a short description for your command',
)]

class PersingCommand extends Command
{
    protected $bus;
    public function __construct(private PersingService $persingService,MessageBusInterface $bus)
    {
        $this->bus = $bus;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Persing',
            '============',
            '',
        ]);
        $this->bus->dispatch(new PerseMessage('https://highload.today/category/novosti/'));
        
        return Command::SUCCESS;
    }
}
