<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\UseCase\Create;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateCommand extends Command
{
    private UserFetcher $users;
    private ValidatorInterface $validator;
    private Create\Handler $handler;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, Create\Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:create')
            ->setDescription('Creates new user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $command = new Create\Command();
        $command->email = (string)$helper->ask($input, $output, new Question('Email: ', ''));
        $command->firstName = (string)$helper->ask($input, $output, new Question('First name: ', ''));
        $command->lastName = (string)$helper->ask($input, $output, new Question('Last name: ', ''));

        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $output->writeln('<error>' . $violation->getPropertyPath() . ': ' . $violation->getMessage()
                    . '</error>');
            }
            return 0;
        }

        $this->handler->handle($command);

        $output->writeln('<info>Done! User successfully created</info>');

        return 0;
    }
}
