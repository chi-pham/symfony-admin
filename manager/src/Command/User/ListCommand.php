<?php

declare(strict_types=1);

namespace App\Command\User;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends Command
{
    private UserFetcher $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:list')
            ->setDescription('Lists all the existing users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allUsers = $this->users->getUsersAsPlainArrays();

        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($input, $bufferedOutput);
        $io->table(
            ['ID', 'Full Name', 'Email', 'Role', 'Status'],
            $allUsers
        );

        $usersAsATable = $bufferedOutput->fetch();
        $output->write($usersAsATable);

        return 0;
    }
}
