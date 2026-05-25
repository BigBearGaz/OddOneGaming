<?php

namespace App\Command;

use App\Entity\Buffs;
use App\Entity\Debuffs;
use App\Entity\Disable;
use App\Twig\SkillExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-effects-from-code',
    description: 'Peuple les tables buffs/debuffs/disable depuis SkillExtension::EFFECTS (idempotent)',
)]
class InitEffectsFromCodeCommand extends Command
{
    private const SKIP = ['Buff', 'Debuff', 'Disable'];

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule sans modifier la DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        $io->title('Import des effets depuis SkillExtension::EFFECTS → DB');

        $counters = ['buff' => [0, 0], 'debuff' => [0, 0], 'disable' => [0, 0]]; // [created, updated]

        foreach (SkillExtension::EFFECTS as $name => $data) {
            if (in_array($name, self::SKIP, true)) {
                continue;
            }

            $type    = $data['type'];
            $desc    = $data['desc'];
            $iconUrl = SkillExtension::iconUrl($name);

            if ($dryRun) {
                $io->writeln(sprintf('  [DRY-RUN] [%s] %s', $type, $name));
                continue;
            }

            $entityClass = match ($type) {
                'buff'    => Buffs::class,
                'debuff'  => Debuffs::class,
                'disable' => Disable::class,
            };

            $entity = $this->em->getRepository($entityClass)->findOneBy(['name' => $name]);
            $isNew  = $entity === null;

            if ($isNew) {
                $entity = new $entityClass();
                $entity->setName($name);
                $entity->setType($type);
            }

            $entity->setDescription($desc);
            $entity->setIconUrl($iconUrl);

            if ($isNew) {
                $this->em->persist($entity);
                $counters[$type][0]++;
                $io->writeln(sprintf('  <comment>+</comment> [%s] %s', $type, $name));
            } else {
                $counters[$type][1]++;
            }
        }

        if ($dryRun) {
            $io->note(sprintf('Dry-run : %d effets seraient traités.', count(SkillExtension::EFFECTS) - count(self::SKIP)));
            return Command::SUCCESS;
        }

        $this->em->flush();

        foreach (['buff', 'debuff', 'disable'] as $type) {
            [$created, $updated] = $counters[$type];
            $io->writeln(sprintf('[%s] %d créés, %d mis à jour', $type, $created, $updated));
        }

        $io->success('Import terminé. Tu peux maintenant éditer les effets via l\'interface admin.');
        return Command::SUCCESS;
    }
}
