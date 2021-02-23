<?php

namespace App\Cli;

use App\Command\CreateUser as CreateUserCommand;
use App\Password\PasswordGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Webmozart\Assert\Assert;

final class CreateUser extends Command
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordGenerator;

    public function __construct(
        MessageBusInterface $messageBus,
        PasswordGeneratorInterface $passwordGenerator
    ) {
        parent::__construct();

        $this->messageBus = $messageBus;
        $this->passwordGenerator = $passwordGenerator;
    }

    public function configure()
    {
        $this
            ->setName('create-user')
            ->setDescription('Creates new user.')
            ->addArgument('email', InputArgument::REQUIRED, 'User e-mail.')
            ->addOption(
                'admin',
                null,
                InputOption::VALUE_NONE,
                'Set this option should the User be an Administrator'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        try {
            Assert::email($email);
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>Invalid e-mail</error>');

            return Command::FAILURE;
        }

        $plainPassword = $this->passwordGenerator->generate(10);

        $this->messageBus->dispatch(new CreateUserCommand($email, $plainPassword, $input->getOption('admin')));

        if ($input->getOption('admin')) {
            $output->writeln(\sprintf('<info>Created Administrator. Password: %s</info>', $plainPassword));
        } else {
            $output->writeln(\sprintf('<info>Created User. Password: %s</info>', $plainPassword));
        }

        return Command::SUCCESS;
    }
}
