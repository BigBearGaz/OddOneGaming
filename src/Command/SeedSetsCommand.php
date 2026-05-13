<?php

namespace App\Command;

use App\Entity\Sets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-sets',
    description: 'Peuple la table des armor sets depuis les données ravenpyros.com',
)]
class SeedSetsCommand extends Command
{
    private const SETS = [
        'Avenger' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5217/5401/6947/ICON_Avenger_ArmorSet.webp',
            2 => 'Heal +15% of damage inflicted when attacking',
            4 => 'Grants +25% chance to counterattack',
            6 => 'Increases damage when counter-attacking by 50%',
        ],
        'Bear' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/1317/5401/6947/ICON_Bear_ArmorSet.webp',
            2 => 'DEF +1% on each turn (max 15 stacks)',
            4 => 'DEF +30%',
            6 => 'Grants a 15% chance to block attack damage.',
        ],
        'Body' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/4417/5401/6947/ICON_Body_ArmorSet.webp',
            2 => 'Increase basic ability damage 15%',
            4 => 'Heal all allies 25% of damage inflicted with basic ability',
            6 => 'Grants 15% chance to gain an extra turn after basic ability',
        ],
        'Brawler' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5817/5401/6947/ICON_Brawler_ArmorSet.webp',
            2 => 'HP +15%',
        ],
        'Claw' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/1517/5401/6947/ICON_Claw_ArmorSet.webp',
            2 => 'CRIT% +10%',
        ],
        'Colossus' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/4817/5401/6945/ICON_Colossus_ArmorSet.webp',
            2 => 'Reduce damage from strong hits +15%',
            4 => 'Gain [Ward] for 1 turn at the start of each wave',
            6 => 'Increase chance to be weak hit +10%',
        ],
        'Defender' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/4417/5401/6946/ICON_Defender_ArmorSet.webp',
            2 => 'DEF +15%',
        ],
        'Disruptor' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/4217/5401/6946/ICON_Disruptor_ArmorSet.webp',
            2 => 'ACC +25',
        ],
        'Dragon' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/6517/5401/6946/ICON_Dragon_ArmorSet.webp',
            2 => 'RES +1% on each turn (max 15 stacks)',
            4 => 'RES +50',
            6 => 'Grants a 30% chance to reflect a blocked or resisted [Debuff]',
        ],
        'Duelist' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3217/5401/6947/ICON_Duelist_ArmorSet.webp',
            2 => 'Decrease damage received from basic ability attacks 20%',
            4 => 'Grants 20% chance to apply [Taunt] for 1 turn when attacking',
            6 => 'Heal (10%HP) when attacked',
        ],
        'Eagle' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/6517/5401/6947/ICON_Eagle_ArmorSet.webp',
            2 => 'INIT +15%',
        ],
        'Falcon' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9017/5401/6946/ICON_Falcon_ArmorSet.webp',
            2 => 'SPD +15%',
        ],
        'Guardian' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3017/5401/6945/ICON_Guardian_ArmorSet.webp',
            2 => 'Redirects 5% of damage inflicted on allies',
            4 => 'Place [Intercept] for 2 turns on the ally with lowest HP',
            6 => 'Reduces damage from ally redirection +30%',
        ],
        'Invoker' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9317/5401/6946/ICON_Invoker_ArmorSet.webp',
            2 => 'RES +25',
        ],
        'Jaguar' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3817/5401/6946/ICON_Jaguar_ArmorSet.webp',
            2 => 'CDMG% +15%',
        ],
        'Kraken' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5617/5401/6947/ICON_Kraken_ArmorSet.webp',
            2 => 'HP +1% on each turn (max 15 stacks)',
            4 => 'HP +30%',
            6 => 'Reduce damage from attacks +15%',
        ],
        'Parasite' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9917/5401/6945/ICON_Parasite_ArmorSet.webp',
            2 => 'Heal (5%HP) when placing a [Debuff]',
            4 => 'Grants 25% chance to place [Corrupt] for 1 turn when attacked',
            6 => 'Increases Divinity 5% after placing a [Debuff]',
        ],
        'Raven' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5017/5401/6946/ICON_Raven_ArmorSet.webp',
            2 => 'ACC +1% on each turn (max 15 stacks)',
            4 => 'ACC +50',
            6 => 'Increase [Debuff] placement chance 10%',
        ],
        'Razor' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/8117/6788/2540/Set_Icons_26.webp',
            2 => 'Inflict 10% increased damage to enemies under a [Debuff]',
            4 => 'Grants 25% chance to place 1 stack of [Bleed] when attacked',
            6 => 'Increase turn meter 5% after placing a [Debuff]',
        ],
        'Shrine' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9917/5508/2342/ICON_Shrine_ArmorSet.webp',
            2 => 'Increase healing provided 15%',
            4 => 'Heal (10%) at the start of turn',
            6 => 'Heal all allies 5% of their MaxHP at the start of turn',
        ],
        'Slayer' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3517/5401/6945/ICON_Slayer_ArmorSet.webp',
            2 => 'ATK +15%',
        ],
        'Soul' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5517/5401/6946/ICON_Soul_ArmorSet.webp',
            2 => 'Increase Ultimate Ability Damage +15%',
            4 => 'Increase Divinity 10% after ultimate ability',
            6 => 'Reduce ultimate ability Divinity cost +15%',
        ],
        'Specter' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5317/5401/6947/ICON_Specter_ArmorSet.webp',
            2 => 'Reduce damage received from area attacks 15%',
            4 => 'Gain [Vanish] for 2 turns at the start of each wave',
            6 => 'Grants 25% chance to block effects from area abilities',
        ],
        'Tremor' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5517/5401/6946/ICON_Tremor_ArmorSet.webp',
            2 => 'Increase damage inflicted 15% to enemies under a [Disable]',
            4 => 'Grants 20% chance to place [Stun] for 1 when attacking',
            6 => 'Increase chance to place [Disables] 10%',
        ],
        'Wolf' => [
            'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5317/5401/6946/ICON_Wolf_ArmorSet.webp',
            2 => 'ATK +1% on each turn (max 15 stacks)',
            4 => 'ATK +30%',
            6 => 'Attacks inflict +15% increased damage',
        ],
    ];

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seeding armor sets');

        $repo    = $this->em->getRepository(Sets::class);
        $created = 0;
        $updated = 0;

        foreach (self::SETS as $baseName => $data) {
            $imageUrl = $data['imageUrl'];
            foreach ([2, 4, 6] as $pieces) {
                if (!isset($data[$pieces])) {
                    continue;
                }
                $name = "{$baseName} ({$pieces}-Piece)";
                $set  = $repo->findOneBy(['name' => $name]);
                $isNew = $set === null;
                if ($isNew) {
                    $set = new Sets();
                    $set->setName($name);
                    $this->em->persist($set);
                    $created++;
                } else {
                    $updated++;
                }
                $set->setBaseName($baseName);
                $set->setPieceType($pieces);
                $set->setEffect($data[$pieces]);
                $set->setImageUrl($imageUrl);
            }
        }

        $this->em->flush();
        $io->success(sprintf('%d sets créés, %d mis à jour.', $created, $updated));
        return Command::SUCCESS;
    }
}
