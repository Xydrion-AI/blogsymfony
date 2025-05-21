<?php

namespace App\Command;

use App\Entity\Users;
use App\Utils\Validator;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:add-user',
    description: 'Attention c\'est pour créer un nouvelle utilisateur risque de perte de donneés'
)]

final class AddUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UsersRepository $users,
        private readonly Validator $validator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::OPTIONAL, 'Pseudo de l\'utilisateur')
            ->addArgument('password', InputArgument::OPTIONAL, 'mot de passe de l\'utilisateur')
            ->addArgument('email', InputArgument::OPTIONAL, 'mail de l\'utilisateur')
            ->addArgument('full-name', InputArgument::OPTIONAL, 'Pseudo complet de l\'utilisateur')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Si l\'utilisateur creé par l\'admin')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {

        /** @var string|null $username  */
        $username = $input->getArgument('username');

        /** @var string|null $password  */
        $password = $input->getArgument('password');

        /** @var string|null $email */
        $email = $input->getArgument('email');

        /** @var string|null $full-name */
        $fullName = $input->getArgument('full-name');

        if (null !== $username && null !== $password && null !== $email && null !== $fullName) {
            return;
        }

        $this->io->title('Ajout interactif d\'utilisateur');
        $this->io->text([
            'si vous ne souhaitez pas utiliser cet assistant interactif',
            'fourniser les arguments requis par la ligne de commande suivante',
            '',
            'php bin/console app:add-user username password email@exemple.com',
            '',
            'Maintenant je vous demanderez tout les arguments manquant'
        ]);

        if (null !== $username) {
            $this->io->text('> <info>Nom d\'utilisateur</info>: ' . $username);
        } else {
            $username = $this->io->ask('username', null, $this->validator->validateUsername(...));
            $input->setArgument('username', $username);
        }

        if (null !== $password) {
            $this->io->text('> <info>Mot de passe</info>: ' . u('*')->repeat(u($password)->length()));
        } else {
            $password = $this->io->askHidden('Votre mot de passe sera caché', null, $this->validator->validatePassword(...));
            $input->setArgument('password', $password);
        }

        if (null !== $email) {
            $this->io->text('> <info>Email</info>: ' . $email);
        } else {
            $email = $this->io->ask('email', null, $this->validator->validateEmail(...));
            $input->setArgument('email', $email);
        }

        if (null !== $fullName) {
            $this->io->text('> <info>Nom complet</info>: ' . $fullName);
        } else {
            $fullName = $this->io->ask('fullName', null, $this->validator->validateFullName(...));
            $input->setArgument('full-name', $fullName);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopWatch = new Stopwatch();
        $stopWatch->start('add-user-command');

        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $fullName = $input->getArgument('full-name');
        $isAdmin = $input->getOption('admin');

        $this->validatUserData($username, $plainPassword, $email, $fullName);

        $user = new Users();
        $user->setFullName($fullName);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? Users::ROLE_ADMIN : Users::ROLE_USER]);

        $hachedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hachedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        $this->io->success(\sprintf('%s was successfully created: %s (%s)', $isAdmin ? 'Administrator user' : 'User', $user->getUsername(), $user->getEmail()));

        $event = $stopWatch->stop('add-user-command');

        if ($output->isVerbose()) {
            $this->io->comment(\sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }

    private function validatUserData(string $username, string $plainPassword, string $email, string $fullName): void
    {
        $existingUser = $this->users->findOneBy(['username' => $username]);
        if (null !== $existingUser) {
            throw new RuntimeException(\sprintf('l\'utilisateur "%s" existe déja', $username));
        }
        $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validateFullName($fullName);

        $existingEmail = $this->users->findOneBy(['email' => $email]);
        if (null !== $existingEmail) {
            throw new RuntimeException(\sprintf('l\'email "%s" existe déja', $email));
        }
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command creates new users and saves them in the database:

              <info>php %command.full_name%</info> <comment>username password email</comment>

            By default the command creates regular users. To create administrator users,
            add the <comment>--admin</comment> option:

              <info>php %command.full_name%</info> username password email <comment>--admin</comment>

            If you omit any of the three required arguments, the command will ask you to
            provide the missing values:

              # command will ask you for the email
              <info>php %command.full_name%</info> <comment>username password</comment>

              # command will ask you for the email and password
              <info>php %command.full_name%</info> <comment>username</comment>

              # command will ask you for all arguments
              <info>php %command.full_name%</info>
            HELP;
    }
}