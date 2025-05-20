<?php

namespace App\Command;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:add-user',
    description: 'Attention, c\'est pour créer un nouvel utilisateur, risque de perte de données'
)]

class AddUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHaser,
        private readonly UsersRepository $users
    ) {
        parent::__construct();
    }

    private function getCommandHelp(): string
    {

    }

    protected function configure(): void
    {
        $this  
            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::OPTIONAL, 'Pseudo de l\'utilisateur')
            ->addArgument('password', InputArgument::OPTIONAL, 'Mot de passe de l\'utilisateur')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email de l\'utilisateur')
            ->addArgument('full-name', InputArgument::OPTIONAL, 'Nom complet de l\'utilisateur')
            ->addOption('full-name', InputOption::VALUE_NONE, 'Si l\'utilisateur est créé par un admin')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        
    }
}