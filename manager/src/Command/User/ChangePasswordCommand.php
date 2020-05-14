<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\UseCase\ChangePassword;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangePasswordCommand extends Command
{
    private UserFetcher $users;
    private ValidatorInterface $validator;
    private ChangePassword\Handler $handler;

    public function __construct(UserFetcher $users, ValidatorInterface $validator, ChangePassword\Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:change-password')
            ->setDescription('Change password for user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findByEmail($email)) {
            throw new LogicException('User is not found.');
        }

        $password = $helper->ask($input, $output, new Question('New password: '));
        $command = new ChangePassword\Command($user->id, $password);
        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $output->writeln('<error>' . $violation->getPropertyPath() . ': ' . $violation->getMessage()
                    . '</error>');
            }
            return 1;
        }

        $this->handler->handle($command);
        $output->writeln('<info>Done! Password successfully changed</info>');

        return 0;
    }
}
