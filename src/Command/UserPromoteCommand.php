<?php

namespace App\Command;

use App\Message\User\PromoteUserMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class UserPromoteCommand extends Command
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        parent::__construct();

        $this->messageBus = $messageBus;
    }


    protected function configure()
    {
        $this
            ->setName('app:user:promote')
            ->setDescription('Promotes a user by adding a role')
            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('role', InputArgument::REQUIRED, 'The role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        $wasUpdated = $this->handle(
            new PromoteUserMessage($username, $role)
        );

        if (!$wasUpdated) {
            $io->error(sprintf('User "%s" did already have "%s" role.', $username, $role));

            return Command::FAILURE;
        }

        $io->success(sprintf('Role "%s" has beed added to user "%s".', $role, $username));

        return Command::SUCCESS;
    }
}
