<?php

namespace App\Command;

use App\Message\User\DemoteUserMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class UserDemoteCommand extends Command
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
            ->setName('app:user:demote')
            ->setDescription('Demotes a user by removing a role')
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
            new DemoteUserMessage($username, $role)
        );

        if (!$wasUpdated) {
            $io->error(sprintf('User "%s" didn\'t have have "%s" role.', $username, $role));

            return Command::FAILURE;
        }

        $io->success(sprintf('Role "%s" has beed removed from user "%s".', $role, $username));

        return Command::SUCCESS;
    }
}
