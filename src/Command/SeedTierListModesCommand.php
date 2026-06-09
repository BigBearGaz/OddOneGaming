<?php

namespace App\Command;

use App\Entity\TierListMode;
use App\Repository\TierListModeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-tier-list-modes',
    description: 'Insère les modes de tier list (General, Ascension, Armor, Fafnir) en base',
)]
class SeedTierListModesCommand extends Command
{
    private const MODES = [
        ['slug' => 'overall',             'name' => 'Overall',               'group' => 'General',   'order' => 1],
        ['slug' => 'campaign',            'name' => 'Campaign',              'group' => 'General',   'order' => 2],
        ['slug' => 'dark-tempest',        'name' => 'The Dark Tempest',      'group' => 'Ascension', 'order' => 3],
        ['slug' => 'plague-of-luxor',     'name' => 'The Plague of Luxor',   'group' => 'Ascension', 'order' => 4],
        ['slug' => 'winged-reaper',       'name' => 'The Winged Reaper',     'group' => 'Ascension', 'order' => 5],
        ['slug' => 'desecrated-daimyo',   'name' => 'The Desecrated Daimyo', 'group' => 'Ascension', 'order' => 6],
        ['slug' => 'high-priest-of-ptah', 'name' => 'High Priest of Ptah',  'group' => 'Armor',     'order' => 7],
        ['slug' => 'zmey',                'name' => 'Zmey',                  'group' => 'Armor',     'order' => 8],
        ['slug' => 'colossus-of-asgard',  'name' => 'Colossus of Asgard',   'group' => 'Armor',     'order' => 9],
        ['slug' => 'gwrach-y-rhibyn',     'name' => 'Gwrach Y Rhibyn',      'group' => 'Armor',     'order' => 10],
        ['slug' => 'fafnir',              'name' => 'Fafnir',                'group' => 'Fafnir',    'order' => 11],
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private TierListModeRepository $repo,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seed Tier List Modes');

        $created = 0;
        $skipped = 0;

        foreach (self::MODES as $data) {
            if ($this->repo->findOneBy(['slug' => $data['slug']])) {
                $io->writeln(sprintf('  <comment>~</comment> %s [déjà présent]', $data['name']));
                $skipped++;
                continue;
            }

            $mode = new TierListMode();
            $mode->setSlug($data['slug']);
            $mode->setName($data['name']);
            $mode->setGroupName($data['group']);
            $mode->setSortOrder($data['order']);
            $this->em->persist($mode);
            $created++;
            $io->writeln(sprintf('  <info>+</info> %s (%s)', $data['name'], $data['group']));
        }

        $this->em->flush();
        $io->success(sprintf('%d modes créés, %d déjà existants', $created, $skipped));

        return Command::SUCCESS;
    }
}
