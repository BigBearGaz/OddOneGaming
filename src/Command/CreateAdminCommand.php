<?php
// src/Command/CreateAdminCommand.php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create a new admin user',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🔐 Create New Admin User');

        // Email
        $email = $io->ask('📧 Email', null, function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Invalid email address');
            }
            
            $existingUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $answer]);
            
            if ($existingUser) {
                throw new \RuntimeException('This email is already registered');
            }
            
            return $answer;
        });

        // First Name
        $firstName = $io->ask('👤 First Name (optional)', '');

        // Last Name
        $lastName = $io->ask('👤 Last Name (optional)', '');

        // Password
        $password = $io->askHidden('🔑 Password (min 8 characters)', function ($answer) {
            if (strlen($answer) < 8) {
                throw new \RuntimeException('Password must be at least 8 characters long');
            }
            return $answer;
        });

        // Confirm Password
        $confirmPassword = $io->askHidden('🔑 Confirm Password');

        if ($password !== $confirmPassword) {
            $io->error('Passwords do not match!');
            return Command::FAILURE;
        }

        // Create user
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName ?: null);
        $user->setLastName($lastName ?: null);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);
        $user->setIsActive(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success([
            'Admin user created successfully!',
            sprintf('Email: %s', $email),
            sprintf('Name: %s', $user->getFullName() ?: 'Not provided'),
            'Role: ADMIN',
        ]);

        return Command::SUCCESS;
    }
}
