<?php

namespace App\Command;

use App\Entity\Dungeons;
use App\Entity\DungeonPassive;
use App\Entity\DungeonPhase;
use App\Repository\DungeonsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-dungeons',
    description: 'Insère les 9 boss (Ascension, Armor, Fafnir) depuis godforgedb.app/bosses',
)]
class SeedDungeonsCommand extends Command
{
    private const BASE = 'https://godforgedb.app';

    private const ARMOR_SETS = [
        'Shrine'   => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9917/5508/2342/ICON_Shrine_ArmorSet.webp',
        'Guardian' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3017/5401/6945/ICON_Guardian_ArmorSet.webp',
        'Avenger'  => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5217/5401/6947/ICON_Avenger_ArmorSet.webp',
        'Body'     => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/4417/5401/6947/ICON_Body_ArmorSet.webp',
        'Soul'     => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5517/5401/6946/ICON_Soul_ArmorSet.webp',
        'Tremor'   => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5517/5401/6946/ICON_Tremor_ArmorSet.webp',
        'Duelist'  => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3217/5401/6947/ICON_Duelist_ArmorSet.webp',
        'Razor'    => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/8117/6788/2540/Set_Icons_26.webp',
        'Specter'  => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5317/5401/6947/ICON_Specter_ArmorSet.webp',
        'Parasite' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9917/5401/6945/ICON_Parasite_ArmorSet.webp',
        'Wolf'     => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5317/5401/6946/ICON_Wolf_ArmorSet.webp',
        'Bear'     => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/1317/5401/6947/ICON_Bear_ArmorSet.webp',
        'Dragon'   => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/6517/5401/6946/ICON_Dragon_ArmorSet.webp',
        'Raven'    => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5017/5401/6946/ICON_Raven_ArmorSet.webp',
        'Kraken'   => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5617/5401/6947/ICON_Kraken_ArmorSet.webp',
    ];

    private static function sets(array $names): array
    {
        return array_map(fn($n) => [
            'name'     => $n . ' Set',
            'qty'      => '1',
            'imageUrl' => self::ARMOR_SETS[$n],
            'type'     => 'set',
        ], $names);
    }

    private const SHARED = [
        'mythic-spirit'     => '/images/bosses/abilities/icon-boss-mythic-spirit.png',
        'mythic-tempo'      => '/images/bosses/abilities/icon-boss-mythic-tempo.png',
        'wrath'             => '/images/bosses/abilities/icon-boss-wrath.png',
        'mythic-resilience' => '/images/bosses/abilities/icon-boss-mythic-resilience.png',
    ];

    private const REWARD_ICONS = [
        'Mortal Aetherstones'    => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_101_ICON_MortalAetherstone.png',
        'Ancestral Aetherstones' => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_102_ICON_AncestralAetherstone.png',
        'Whetstone'              => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_301_ICON_Whetstone.png',
        'Raw Ryvenite'           => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_310_ICON_LesserRyvenite.png',
        'Polished Ryvenite'      => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_311_ICON_GreaterRyvenite.png',
        'Perfect Ryvenite'       => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_312_ICON_SuperiorRyvenite.png',
        'Godsteel'               => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_801_ICON_Godsteel.png',
        'Weapon Reroll Stone'    => 'https://www.ravenpyros.com/packages/ravenpyros/images/rewards/currency_813_ICON_CoreWeaponCraftingBoost.png',
        'Weapon'                 => 'https://godforgedb.app/images/rewards/RandomWeapon_Icon.png',
    ];

    private const DUNGEONS = [

        // ══════════════════════════════════════════════
        // ASCENSION — Tower of Strife
        // ══════════════════════════════════════════════

        [
            'name'        => 'The Dark Tempest',
            'imageUrl'    => '/images/bosses/art/the-dark-tempest.webp',
            'description' => 'A Tower of Strife boss built around layered passives, punishing setup turns, and high-pressure ultimate timing.',
            'difficulty'  => 'Ascension',
            'spell1Name'  => 'Unrelenting Darkness',
            'spell1Desc'  => 'Attack all enemies. Reduce the turn meter of targets affected by [ACC Down].',
            'spell1Img'   => '/images/bosses/abilities/icon-the-dark-tempest-basic.png',
            'spell2Name'  => 'Blinded by Darkness',
            'spell2Desc'  => 'Attack all enemies. Apply [ACC Down III] on all enemies for 2 turns.',
            'spell2Img'   => '/images/bosses/abilities/icon-the-dark-tempest-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Eternal Darkness',
            'spell3Desc'  => 'Remove all debuffs on self. Heal 50% HP. Apply [DEF Up III] on self for 3 turns. Costs 3000 Divinity.',
            'spell3Img'   => '/images/bosses/abilities/icon-the-dark-tempest-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                ['name' => 'Gold',                  'qty' => '600',  'imageUrl' => '/images/rewards/ICON_Gold.png'],
                ['name' => 'Dungeon Medal',          'qty' => '1',    'imageUrl' => '/images/rewards/ICON_MEDAL_3Affinities.png'],
                ['name' => 'Lesser Strength Stone', 'qty' => '1–3',  'imageUrl' => '/images/rewards/ICON_AlchemyStones_LesserStrength.png'],
            ],
            'passives' => [
                ['name' => 'Strength of Corruption', 'desc' => 'Reduces damage taken by 50% if not affected by any debuff. Gains 150 Divinity when attacked. Immune to Disable.',    'order' => 1, 'img' => '/images/bosses/abilities/icon-the-dark-tempest-strength-of-corruption.png'],
                ['name' => 'Exposed Weakness',        'desc' => 'Deals 15% extra damage against enemies with debuffs. Gains 50 Divinity when attacking an enemy with a debuff.',     'order' => 2, 'img' => '/images/bosses/abilities/icon-the-dark-tempest-exposed-weakness.png'],
                ['name' => 'Mythic Spirit',           'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                                                               'order' => 3, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',            'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                                                    'order' => 4, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',                   'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',                                      'order' => 5, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',       'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',                                            'order' => 6, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        [
            'name'        => 'The Plague of Luxor',
            'imageUrl'    => '/images/bosses/art/the-plague-of-luxor.webp',
            'description' => 'A Tower of Strife boss that leverages debuff chains, punishing setup turns, and brutal ultimate timing.',
            'difficulty'  => 'Ascension',
            'spell1Name'  => 'Shared Corruption',
            'spell1Desc'  => 'Attack all enemies (300% ATK). Place [Vulnerable III] on all enemies for 2 turns before attacking.',
            'spell1Img'   => '/images/bosses/abilities/icon-the-plague-of-luxor-basic.png',
            'spell2Name'  => 'Plaguebourne',
            'spell2Desc'  => 'Attack all enemies (300% ATK). Activate all debuffs on enemies without reducing their duration. Costs 1000 Divinity.',
            'spell2Img'   => '/images/bosses/abilities/icon-the-plague-of-luxor-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Affliction of Aether',
            'spell3Desc'  => 'Attack all enemies (300% ATK). Apply debuffs based on each enemy\'s affinity ([Acid III], [Bleed III], [Blaze III] or [Aetherburn III]) for 3 turns. Costs 3500 Divinity.',
            'spell3Img'   => '/images/bosses/abilities/icon-the-plague-of-luxor-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                ['name' => 'Gold',                 'qty' => '600', 'imageUrl' => '/images/rewards/ICON_Gold.png'],
                ['name' => 'Dungeon Medal',         'qty' => '1',   'imageUrl' => '/images/rewards/ICON_MEDAL_3Affinities.png'],
                ['name' => 'Lesser Cunning Stone', 'qty' => '1–3', 'imageUrl' => '/images/rewards/ICON_AlchemyStone_GreaterCunning.png'],
            ],
            'passives' => [
                ['name' => 'Unstoppable Corruption', 'desc' => 'Gains 20 Divinity whenever enemies receive debuff damage. Immune to Disable.',                               'order' => 1, 'img' => '/images/bosses/abilities/icon-the-plague-of-luxor-unstoppable-corruption.png'],
                ['name' => 'Call the Plague',        'desc' => 'Activates Affliction of Aether at the start of battle. Gains 5% turn meter per active debuff on enemies.',  'order' => 2, 'img' => '/images/bosses/abilities/icon-the-plague-of-luxor-call-the-plague.png'],
                ['name' => 'Mythic Spirit',           'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                                                      'order' => 3, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',            'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                                           'order' => 4, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',                   'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',                             'order' => 5, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',       'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock Core/Ultimate/Passive and Charm.',             'order' => 6, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        [
            'name'        => 'The Winged Reaper',
            'imageUrl'    => '/images/bosses/art/the-winged-reaper.webp',
            'description' => 'A Tower of Strife boss built around layered passives, punishing setup turns, and high-pressure ultimate timing.',
            'difficulty'  => 'Ascension',
            'spell1Name'  => 'Umbral Weakening',
            'spell1Desc'  => 'Attack all enemies. Apply [ATK Down III] on all enemies for 2 turns.',
            'spell1Img'   => '/images/bosses/abilities/icon-the-winged-reaper-basic.png',
            'spell2Name'  => 'Wings of Corruption',
            'spell2Desc'  => 'Attack all enemies. Apply [DEF Down III] on all enemies for 2 turns.',
            'spell2Img'   => '/images/bosses/abilities/icon-the-winged-reaper-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Shadow Siphon',
            'spell3Desc'  => 'Attack all enemies. Reduce Divinity of all enemies by 30%. Increase Divinity cost of all enemies by 20%. Costs 4000 Divinity.',
            'spell3Img'   => '/images/bosses/abilities/icon-the-winged-reaper-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                ['name' => 'Gold',                'qty' => '600', 'imageUrl' => '/images/rewards/ICON_Gold.png'],
                ['name' => 'Dungeon Medal',        'qty' => '1',   'imageUrl' => '/images/rewards/ICON_MEDAL_3Affinities.png'],
                ['name' => 'Lesser Wisdom Stone', 'qty' => '1–3', 'imageUrl' => '/images/rewards/ICON_AlchemyStones_LesserWisdom.png'],
            ],
            'passives' => [
                ['name' => 'Shadow Corruption',  'desc' => 'Passively applies a stacking shadow corruption effect that escalates damage over time.',  'order' => 1, 'img' => '/images/bosses/abilities/icon-the-winged-reaper-shadow-corruption.png'],
                ['name' => 'Shadow Escalation',  'desc' => 'Each turn, the shadow corruption stacks intensify, further increasing damage output.',    'order' => 2, 'img' => '/images/bosses/abilities/icon-the-winged-reaper-shadow-escalation.png'],
                ['name' => 'Mythic Tempo',        'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                        'order' => 3, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',               'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',          'order' => 4, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',   'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',                 'order' => 5, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        [
            'name'        => 'The Desecrated Daimyo',
            'imageUrl'    => '/images/bosses/art/the-desecrated-daimyo.webp',
            'description' => 'A Tower of Strife boss built around layered passives, punishing setup turns, and high-pressure ultimate timing.',
            'difficulty'  => 'Ascension',
            'spell1Name'  => 'Conqueror\'s Blade',
            'spell1Desc'  => 'Attack all enemies (250% ATK). Place [FTH Down III] on all enemies for 1 turn.',
            'spell1Img'   => '/images/bosses/abilities/icon-the-desecrated-daimyo-basic.png',
            'spell2Name'  => 'Daimyo\'s Decree',
            'spell2Desc'  => 'Attack all enemies (250% ATK). Place [SPD Down III] on all enemies for 2 turns.',
            'spell2Img'   => '/images/bosses/abilities/icon-the-desecrated-daimyo-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Eternal Corruption',
            'spell3Desc'  => 'Remove all debuffs on self. Place [Barrier] (30% HP) on self. Reduce own Divinity cost by 500. Costs 4000 Divinity.',
            'spell3Img'   => '/images/bosses/abilities/icon-the-desecrated-daimyo-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                ['name' => 'Gold',                 'qty' => '600', 'imageUrl' => '/images/rewards/ICON_Gold.png'],
                ['name' => 'Dungeon Medal',         'qty' => '1',   'imageUrl' => '/images/rewards/ICON_MEDAL_3Affinities.png'],
                ['name' => 'Lesser Eternal Stone', 'qty' => '1–3', 'imageUrl' => '/images/rewards/ICON_AlchemyStone_LesserEternal.png'],
            ],
            'passives' => [
                ['name' => 'Desecrated Divination', 'desc' => 'Increases Divinity by 200 and heals 10% HP when attacked while under a [Barrier]. Immune to Disable.',  'order' => 1, 'img' => '/images/bosses/abilities/icon-the-desecrated-daimyo-desecrated-divination.png'],
                ['name' => 'Relentless Strike',      'desc' => 'Activates Conqueror\'s Blade when attacked while under a [Barrier].',                                   'order' => 2, 'img' => '/images/bosses/abilities/icon-the-desecrated-daimyo-relentless-strike.png'],
                ['name' => 'Mythic Spirit',           'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                                                 'order' => 3, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',            'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                                      'order' => 4, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',                   'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',                        'order' => 5, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',       'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',                               'order' => 6, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        // ══════════════════════════════════════════════
        // ARMOR
        // ══════════════════════════════════════════════

        [
            'name'        => 'High Priest of Ptah',
            'imageUrl'    => '/images/bosses/art/high-priest-of-ptah.webp',
            'description' => 'An adventure boss with passive-heavy mechanics, divinity pressure, and an ultimate pattern that shapes the fight.',
            'difficulty'  => 'Armor',
            'spell1Name'  => null, 'spell1Desc' => null, 'spell1Img' => null,
            'spell2Name'  => null, 'spell2Desc' => null, 'spell2Img' => null, 'spell2CD' => null,
            'spell3Name'  => null, 'spell3Desc' => null, 'spell3Img' => null, 'spell3CD' => null,
            'rewards'     => [
                'gearPool' => [
                    ['name' => 'Shrine Set',   'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9917/5508/2342/ICON_Shrine_ArmorSet.webp'],
                    ['name' => 'Guardian Set', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3017/5401/6945/ICON_Guardian_ArmorSet.webp'],
                    ['name' => 'Avenger Set',  'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5217/5401/6947/ICON_Avenger_ArmorSet.webp'],
                    ['name' => 'Body Set',     'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/4417/5401/6947/ICON_Body_ArmorSet.webp'],
                    ['name' => 'Soul Set',     'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5517/5401/6946/ICON_Soul_ArmorSet.webp'],
                ],
                'stages' => [
                    ['s' =>  1, 'energy' =>  6, 'gold' => 1100, 'tov' => 1, 'xp' => 2700, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  3.3],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  3.3],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 90.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                    ]],
                    ['s' =>  2, 'energy' =>  6, 'gold' => 1100, 'tov' => 1, 'xp' => 2800, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  3.2],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  3.2],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 75.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 12.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                    ]],
                    ['s' =>  3, 'energy' =>  6, 'gold' => 1100, 'tov' => 1, 'xp' => 2925, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  3.1],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  3.1],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 60.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 25.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                    ]],
                    ['s' =>  4, 'energy' =>  8, 'gold' => 1550, 'tov' => 2, 'xp' => 3275, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.0],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 25.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 40.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 12.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  7.5],
                    ]],
                    ['s' =>  5, 'energy' =>  8, 'gold' => 1550, 'tov' => 2, 'xp' => 3430, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.9],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.9],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 45.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 27.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 12.5],
                    ]],
                    ['s' =>  6, 'energy' =>  8, 'gold' => 1550, 'tov' => 2, 'xp' => 3600, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.8],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.8],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 40.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 25.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 60, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 20.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' =>  5.0],
                    ]],
                    ['s' =>  7, 'energy' => 10, 'gold' => 2000, 'tov' => 3, 'xp' => 3800, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.7],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.7],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 20.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 60, 'rate' => 30.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 70, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' =>  5.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 10.0],
                    ]],
                    ['s' =>  8, 'energy' => 10, 'gold' => 2000, 'tov' => 3, 'xp' => 4200, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  1.7],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  1.7],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  1.7],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 60, 'rate' => 28.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 70, 'rate' => 28.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 10.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 12.5],
                    ]],
                    ['s' =>  9, 'energy' => 10, 'gold' => 2000, 'tov' => 3, 'xp' => 4400, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.5],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 70, 'rate' => 35.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 17.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  2.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 15.0],
                    ]],
                    ['s' => 10, 'energy' => 12, 'gold' => 2500, 'tov' => 5, 'xp' => 5000, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.4],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.4],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 37.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>100, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  4.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 20.0],
                    ]],
                    ['s' => 11, 'energy' => 12, 'gold' => 2500, 'tov' => 5, 'xp' => 5500, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.3],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.3],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 22.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>100, 'rate' => 22.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  5.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 22.5],
                    ]],
                    ['s' => 12, 'energy' => 12, 'gold' => 2500, 'tov' => 5, 'xp' => 6000, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  1.5],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  1.5],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  1.5],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 10.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>100, 'rate' => 22.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>110, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 12.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  8.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 25.0],
                    ]],
                    ['s' => 13, 'energy' => 14, 'gold' => 3050, 'tov' => 6, 'xp' => 6600, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.1],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  2.1],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>110, 'rate' => 18.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>130, 'rate' => 18.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  2.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' => 10.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 30.0],
                    ]],
                    ['s' => 14, 'energy' => 14, 'gold' => 3050, 'tov' => 6, 'xp' => 7000, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  2.0],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>130, 'rate' => 35.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  2.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 10.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' => 15.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 31.5],
                    ]],
                    ['s' => 15, 'energy' => 14, 'gold' => 3050, 'tov' => 6, 'xp' => 7500, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  1.9],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  1.9],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>130, 'rate' => 15.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>150, 'rate' => 19.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  2.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' => 10, 'rate' =>  5.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 33.0],
                    ]],
                ],
            ],
            'passives' => [
                ['name' => 'Mythic Spirit',      'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                         'order' => 1, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Wrath',              'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.', 'order' => 2, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',  'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',       'order' => 3, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [
                [
                    'name'   => 'Veil of Ptah',
                    'order'  => 1,
                    'spell1' => ['name' => 'Purge the Veil',    'desc' => 'Attack a single target. Inflict [Acid] on the target.',                                                              'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-basic-phase1.png'],
                    'spell2' => ['name' => 'Consume Knowledge', 'desc' => 'Attack all enemies. Apply [Shield] or [Barrier] on self. Inflict [Aetherburn] on all enemies.', 'cd' => 3,          'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-core-phase1.png'],
                    'spell3' => ['name' => 'Invoke Isfet',      'desc' => 'Sacrifice all allied minions to gain massive Divinity and deal heavy damage to all enemies.',   'cd' => null,        'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-ultimate-phase1.png'],
                    'passives' => [
                        ['name' => 'Protection of Ptah', 'desc' => 'Generates stacks of Might of Ptah each turn, increasing damage and defense.',  'order' => 1, 'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-protection-passive.png'],
                        ['name' => 'Union of Ptah',      'desc' => 'Converts a portion of damage received into a [Barrier] or healing effect.',    'order' => 2, 'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-union-passive.png'],
                    ],
                ],
                [
                    'name'   => 'Ma\'at Restored',
                    'order'  => 2,
                    'spell1' => ['name' => 'Purge all things!',    'desc' => 'Attack all enemies. Inflict [Acid] on all targets.',                                                        'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-basic-phase2.png'],
                    'spell2' => ['name' => 'Extinguish Life',      'desc' => 'Execute all enemies and allies below a HP threshold.', 'cd' => 3,                                          'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-core-phase2.png'],
                    'spell3' => ['name' => 'Restoration of Ma\'at','desc' => 'Resurrect all defeated allied minions with a portion of their HP.', 'cd' => null,                         'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-ultimate-phase2.png'],
                    'passives' => [
                        ['name' => 'Dereliction of Ma\'at', 'desc' => 'Cannot generate Divinity while in Isfet form.',                           'order' => 1, 'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-dereliction-passive.png'],
                        ['name' => 'Divination of Duat',    'desc' => 'Generates Divinity whenever an enemy\'s turn meter is reduced.',           'order' => 2, 'img' => '/images/bosses/abilities/icon-high-priest-of-ptah-divination-passive.png'],
                    ],
                ],
            ],
        ],

        [
            'name'        => 'Zmey',
            'imageUrl'    => '/images/bosses/art/zmey.webp',
            'description' => 'An adventure boss with passive-heavy mechanics, divinity pressure, and an ultimate pattern that shapes the fight.',
            'difficulty'  => 'Armor',
            'spell1Name'  => 'Mischievous Theft',
            'spell1Desc'  => 'Attack all enemies (150% ATK + 1% ATK per FTH). Steal 1 buff from each target.',
            'spell1Img'   => '/images/bosses/abilities/icon-zmey-basic.png',
            'spell2Name'  => 'Relentless Hunger',
            'spell2Desc'  => 'Place 1 random [Buff] on self for 2 turns, then gain an extra turn.',
            'spell2Img'   => '/images/bosses/abilities/icon-zmey-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Union of Greed',
            'spell3Desc'  => 'Double current Gluttony stacks. Attack all enemies 3 times. Destroy 5%–50% HP of all enemies based on Gluttony stacks.',
            'spell3Img'   => '/images/bosses/abilities/icon-zmey-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                'gearPool' => [
                    ['name' => 'Tremor Set',  'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5517/5401/6946/ICON_Tremor_ArmorSet.webp'],
                    ['name' => 'Duelist Set', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/3217/5401/6947/ICON_Duelist_ArmorSet.webp'],
                    ['name' => 'Razor Set',   'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/8117/6788/2540/Set_Icons_26.webp'],
                    ['name' => 'Specter Set', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5317/5401/6947/ICON_Specter_ArmorSet.webp'],
                    ['name' => 'Parasite Set','imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/9917/5401/6945/ICON_Parasite_ArmorSet.webp'],
                ],
                'stages' => [
                    ['s' =>  1, 'energy' =>  6, 'gold' => 1100, 'tov' => 1, 'xp' => 2700, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  3.3],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  3.3],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 90.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                    ]],
                    ['s' =>  2, 'energy' =>  6, 'gold' => 1100, 'tov' => 1, 'xp' => 2800, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  3.2],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  3.2],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 75.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 12.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                    ]],
                    ['s' =>  3, 'energy' =>  6, 'gold' => 1100, 'tov' => 1, 'xp' => 2925, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  3.1],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  3.1],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 60.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 25.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                    ]],
                    ['s' =>  4, 'energy' =>  8, 'gold' => 1550, 'tov' => 2, 'xp' => 3275, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  1, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.0],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 30, 'rate' => 25.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 40.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 12.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  7.5],
                    ]],
                    ['s' =>  5, 'energy' =>  8, 'gold' => 1550, 'tov' => 2, 'xp' => 3430, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.9],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.9],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 45.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 27.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 12.5],
                    ]],
                    ['s' =>  6, 'energy' =>  8, 'gold' => 1550, 'tov' => 2, 'xp' => 3600, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.8],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.8],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 40, 'rate' => 40.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 25.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 60, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 20.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' =>  5.0],
                    ]],
                    ['s' =>  7, 'energy' => 10, 'gold' => 2000, 'tov' => 3, 'xp' => 3800, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  2.7],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.7],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 50, 'rate' => 20.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 60, 'rate' => 30.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 70, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' =>  5.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 10.0],
                    ]],
                    ['s' =>  8, 'energy' => 10, 'gold' => 2000, 'tov' => 3, 'xp' => 4200, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  2, 'rate' =>  1.7],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  1.7],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  1.7],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 60, 'rate' => 28.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 70, 'rate' => 28.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 10.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 12.5],
                    ]],
                    ['s' =>  9, 'energy' => 10, 'gold' => 2000, 'tov' => 3, 'xp' => 4400, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.5],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 70, 'rate' => 35.0],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 17.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  2.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 15.0],
                    ]],
                    ['s' => 10, 'energy' => 12, 'gold' => 2500, 'tov' => 5, 'xp' => 5000, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.4],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.4],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 37.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>100, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 11.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  4.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 20.0],
                    ]],
                    ['s' => 11, 'energy' => 12, 'gold' => 2500, 'tov' => 5, 'xp' => 5500, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  2.3],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.3],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 22.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>100, 'rate' => 22.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  5.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 22.5],
                    ]],
                    ['s' => 12, 'energy' => 12, 'gold' => 2500, 'tov' => 5, 'xp' => 6000, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  3, 'rate' =>  1.5],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  1.5],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  1.5],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' => 80, 'rate' => 10.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>100, 'rate' => 22.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>110, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  7.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 12.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' =>  8.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 25.0],
                    ]],
                    ['s' => 13, 'energy' => 14, 'gold' => 3050, 'tov' => 6, 'xp' => 6600, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.1],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  2.1],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>110, 'rate' => 18.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>130, 'rate' => 18.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  2.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' => 10.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 30.0],
                    ]],
                    ['s' => 14, 'energy' => 14, 'gold' => 3050, 'tov' => 6, 'xp' => 7000, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  2.0],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>130, 'rate' => 35.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  6, 'rate' =>  2.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  5.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 10.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' => 15.0],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 31.5],
                    ]],
                    ['s' => 15, 'energy' => 14, 'gold' => 3050, 'tov' => 6, 'xp' => 7500, 'drops' => [
                        ['name' => 'Mortal Aetherstones',    'qty' =>  4, 'rate' =>  1.9],
                        ['name' => 'Mortal Aetherstones',    'qty' =>  5, 'rate' =>  1.9],
                        ['name' => 'Ancestral Aetherstones', 'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',              'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Raw Ryvenite',           'qty' =>130, 'rate' => 15.0],
                        ['name' => 'Raw Ryvenite',           'qty' =>150, 'rate' => 19.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  7, 'rate' =>  2.5],
                        ['name' => 'Polished Ryvenite',      'qty' =>  8, 'rate' => 10.0],
                        ['name' => 'Polished Ryvenite',      'qty' =>  9, 'rate' => 15.0],
                        ['name' => 'Polished Ryvenite',      'qty' => 10, 'rate' =>  5.5],
                        ['name' => 'Perfect Ryvenite',       'qty' =>  1, 'rate' => 33.0],
                    ]],
                ],
            ],
            'passives' => [
                ['name' => 'Gluttonous',            'desc' => 'Gains 1 stack of Gluttony each time a buff is acquired. At the start of each turn, removes all Disable effects.',       'order' => 1, 'img' => '/images/bosses/abilities/icon-zmey-gluttony-passive.png'],
                ['name' => 'Multi-Headed Weakness', 'desc' => 'At 10 stacks of Gluttony, loses 33% HP. At 6 stacks, steals a buff from all enemies. Loses 3 stacks per debuff.',      'order' => 2, 'img' => '/images/bosses/abilities/icon-zmey-multi-head-passive.png'],
                ['name' => 'Mythic Spirit',          'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                                                                 'order' => 3, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',           'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                                                      'order' => 4, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',                  'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',                                        'order' => 5, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',      'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',                                               'order' => 6, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        [
            'name'        => 'Colossus of Asgard',
            'imageUrl'    => '/images/bosses/art/colossus-of-asgard.webp',
            'description' => 'An adventure boss with passive-heavy mechanics, divinity pressure, and an ultimate pattern that shapes the fight.',
            'difficulty'  => 'Armor',
            'spell1Name'  => 'Might of Brokkr',
            'spell1Desc'  => 'Attack all enemies (300% ATK). Apply [DEF Down III] and [SPD Down III] on all enemies for 2 turns.',
            'spell1Img'   => '/images/bosses/abilities/icon-colossus-of-asgard-basic.png',
            'spell2Name'  => 'Fury of the Forge',
            'spell2Desc'  => 'Attack all enemies (300% ATK). Apply [Aetherburn III] on all enemies for 3 turns. Increase own Divinity by 50% for each defeated enemy.',
            'spell2Img'   => '/images/bosses/abilities/icon-colossus-of-asgard-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Guardian of the Forge',
            'spell3Desc'  => 'Attack all enemies. Remove all [Disable] effects from allied Forge constructs. Place [Intercept] and [Barrier] on allied constructs.',
            'spell3Img'   => '/images/bosses/abilities/icon-colossus-of-asgard-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                ['name' => 'Wolf Set',                 'qty' => '1', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5317/5401/6946/ICON_Wolf_ArmorSet.webp',   'type' => 'set'],
                ['name' => 'Bear Set',                 'qty' => '1', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/1317/5401/6947/ICON_Bear_ArmorSet.webp',   'type' => 'set'],
                ['name' => 'Dragon Set',               'qty' => '1', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/6517/5401/6946/ICON_Dragon_ArmorSet.webp', 'type' => 'set'],
                ['name' => 'Raven Set',                'qty' => '1', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5017/5401/6946/ICON_Raven_ArmorSet.webp',  'type' => 'set'],
                ['name' => 'Kraken Set',               'qty' => '1', 'imageUrl' => 'https://www.ravenpyros.com/application/files/thumbnails/file_manager_listing/5617/5401/6947/ICON_Kraken_ArmorSet.webp', 'type' => 'set'],
                ['name' => 'Gold',                    'qty' => '850', 'imageUrl' => 'https://godforgedb.app/images/rewards/ICON_Gold.png'],
                ['name' => 'Weapon Crafting Material', 'qty' => '2',   'imageUrl' => 'https://godforgedb.app/images/rewards/ICON_WeaponCrafting.png'],
                ['name' => 'Mortal Aetherstone',       'qty' => '1–2', 'imageUrl' => 'https://godforgedb.app/images/rewards/ICON_MortalAetherstone.png'],
                ['name' => 'Ancestral Aetherstone',    'qty' => '1',   'imageUrl' => 'https://godforgedb.app/images/rewards/ICON_AncestralAetherstone.png'],
                ['name' => 'Forge Weapon Pool',        'qty' => '1',   'imageUrl' => 'https://godforgedb.app/images/rewards/RandomWeapon_Icon.png'],
            ],
            'passives' => [
                ['name' => 'Forge Malfunction',   'desc' => 'Removes Overburn stacks when Dwarven Bellows are Crippled. Increases damage received while Crippled.',  'order' => 1, 'img' => '/images/bosses/abilities/icon-colossus-of-asgard-malfunction-passive.png'],
                ['name' => 'Dwarven Restoration', 'desc' => 'Heals 3% max HP when taking debuff damage. Heals 1% max HP when attacking while under [Barrier].',      'order' => 2, 'img' => '/images/bosses/abilities/icon-colossus-of-asgard-restoration-passive.png'],
                ['name' => 'Mythic Spirit',        'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                                                  'order' => 3, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',         'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                                       'order' => 4, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',                'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',                         'order' => 5, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',    'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',                                'order' => 6, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        [
            'name'        => 'Gwrach Y Rhibyn',
            'imageUrl'    => '/images/bosses/art/gwrach-yrhibyn.webp',
            'description' => 'An adventure boss with passive-heavy mechanics, divinity pressure, and an ultimate pattern that shapes the fight.',
            'difficulty'  => 'Armor',
            'spell1Name'  => 'Hag\'s Reap',
            'spell1Desc'  => 'Attack a single enemy (300% ATK). Ignore 30% DEF if the target is not being [Intercepted]. Steal 30% of the target\'s turn meter.',
            'spell1Img'   => '/images/bosses/abilities/icon-gwrach-yrhibyn-basic.png',
            'spell2Name'  => 'Dread Mist',
            'spell2Desc'  => 'Attack all enemies (250% ATK). Apply [Despair] on all enemies for 1 turn. Create shields on all active Totems.',
            'spell2Img'   => '/images/bosses/abilities/icon-gwrach-yrhibyn-core.png',
            'spell2CD'    => 3,
            'spell3Name'  => 'Vengeance of the Exiled',
            'spell3Desc'  => 'Attack all enemies (250% ATK). Deal 20% extra damage for each living Totem.',
            'spell3Img'   => '/images/bosses/abilities/icon-gwrach-yrhibyn-ultimate.png',
            'spell3CD'    => null,
            'rewards'     => [
                'stages' => [
                    ['s' =>  1, 'energy' =>  6, 'gold' =>  850, 'tov' => 1, 'xp' => 2400, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  1, 'rate' => 19.3],
                        ['name' => 'Godsteel',              'qty' =>  2, 'rate' => 77.2],
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' =>  3.5],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  1, 'rate' =>  3.4],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  3.4],
                    ]],
                    ['s' =>  2, 'energy' =>  6, 'gold' =>  850, 'tov' => 1, 'xp' => 2500, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  1, 'rate' => 16.5],
                        ['name' => 'Godsteel',              'qty' =>  2, 'rate' => 66.1],
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' => 17.4],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  1, 'rate' =>  3.3],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  3.3],
                    ]],
                    ['s' =>  3, 'energy' =>  6, 'gold' =>  850, 'tov' => 1, 'xp' => 2650, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  1, 'rate' => 14.3],
                        ['name' => 'Godsteel',              'qty' =>  2, 'rate' => 57.1],
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' => 28.6],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 10, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  1, 'rate' =>  3.2],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  3.2],
                    ]],
                    ['s' =>  4, 'energy' =>  8, 'gold' => 1200, 'tov' => 2, 'xp' => 2950, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  2, 'rate' => 14.5],
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' => 38.7],
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' => 29.0],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 10.6],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' =>  4.8],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  2.4],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  1, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.0],
                    ]],
                    ['s' =>  5, 'energy' =>  8, 'gold' => 1200, 'tov' => 2, 'xp' => 3100, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  2, 'rate' =>  9.5],
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' => 38.1],
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' => 28.6],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 14.3],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' =>  4.8],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  2.4],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  2.4],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.1],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  2.9],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.9],
                    ]],
                    ['s' =>  6, 'energy' =>  8, 'gold' => 1200, 'tov' => 2, 'xp' => 3300, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  2, 'rate' =>  8.4],
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' => 29.4],
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' => 33.6],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 16.8],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' =>  7.6],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  2.1],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  2.1],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.1],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 15, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  2.8],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.8],
                    ]],
                    ['s' =>  7, 'energy' => 10, 'gold' => 1500, 'tov' => 3, 'xp' => 3800, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' =>  4.1],
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' => 24.4],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 40.7],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' => 16.3],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  8.1],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  4.1],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' =>  2.3],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.2],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  2.7],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.7],
                    ]],
                    ['s' =>  8, 'energy' => 10, 'gold' => 1500, 'tov' => 3, 'xp' => 4100, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  3, 'rate' =>  3.8],
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' => 18.8],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 33.8],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' => 22.6],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' => 11.3],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  7.5],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' =>  2.3],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.2],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  2, 'rate' =>  1.7],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  1.7],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  1.7],
                    ]],
                    ['s' =>  9, 'energy' => 10, 'gold' => 1500, 'tov' => 3, 'xp' => 4300, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' => 15.8],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 35.4],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' => 23.6],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' => 15.0],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  7.9],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' =>  2.4],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.3],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 25, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  2.5],
                    ]],
                    ['s' => 10, 'energy' => 12, 'gold' => 1600, 'tov' => 5, 'xp' => 5000, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  4, 'rate' =>  3.0],
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 14.8],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' => 20.7],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' => 23.6],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' => 17.7],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' => 11.8],
                        ['name' => 'Godsteel',              'qty' => 10, 'rate' =>  5.9],
                        ['name' => 'Godsteel',              'qty' => 11, 'rate' =>  2.7],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.3],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.4],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  2.4],
                    ]],
                    ['s' => 11, 'energy' => 12, 'gold' => 1600, 'tov' => 5, 'xp' => 5200, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' => 12.6],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' => 18.8],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' => 25.1],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' => 18.8],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' => 12.6],
                        ['name' => 'Godsteel',              'qty' => 10, 'rate' =>  6.3],
                        ['name' => 'Godsteel',              'qty' => 11, 'rate' =>  3.1],
                        ['name' => 'Godsteel',              'qty' => 12, 'rate' =>  1.6],
                        ['name' => 'Godsteel',              'qty' => 13, 'rate' =>  1.1],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.4],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  2.3],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  2.3],
                    ]],
                    ['s' => 12, 'energy' => 12, 'gold' => 1600, 'tov' => 5, 'xp' => 5600, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  5, 'rate' =>  8.5],
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' => 14.1],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' => 28.3],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' => 21.2],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' => 14.1],
                        ['name' => 'Godsteel',              'qty' => 10, 'rate' =>  7.1],
                        ['name' => 'Godsteel',              'qty' => 11, 'rate' =>  3.5],
                        ['name' => 'Godsteel',              'qty' => 12, 'rate' =>  1.8],
                        ['name' => 'Godsteel',              'qty' => 13, 'rate' =>  1.4],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.4],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 30, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  3, 'rate' =>  1.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  1.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  5, 'rate' =>  1.5],
                    ]],
                    ['s' => 13, 'energy' => 14, 'gold' => 1700, 'tov' => 6, 'xp' => 6400, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  6, 'rate' =>  0.9],
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  8.5],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' => 17.0],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' => 33.9],
                        ['name' => 'Godsteel',              'qty' => 10, 'rate' => 21.2],
                        ['name' => 'Godsteel',              'qty' => 11, 'rate' => 12.7],
                        ['name' => 'Godsteel',              'qty' => 12, 'rate' =>  4.2],
                        ['name' => 'Godsteel',              'qty' => 13, 'rate' =>  1.7],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  2.1],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  5, 'rate' =>  2.1],
                    ]],
                    ['s' => 14, 'energy' => 14, 'gold' => 1700, 'tov' => 6, 'xp' => 6900, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  4.7],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  9.4],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' => 37.5],
                        ['name' => 'Godsteel',              'qty' => 10, 'rate' => 23.4],
                        ['name' => 'Godsteel',              'qty' => 11, 'rate' => 14.0],
                        ['name' => 'Godsteel',              'qty' => 12, 'rate' =>  7.5],
                        ['name' => 'Godsteel',              'qty' => 13, 'rate' =>  2.8],
                        ['name' => 'Godsteel',              'qty' => 14, 'rate' =>  0.8],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  2.0],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  5, 'rate' =>  2.0],
                    ]],
                    ['s' => 15, 'energy' => 14, 'gold' => 1700, 'tov' => 6, 'xp' => 7500, 'drops' => [
                        ['name' => 'Godsteel',              'qty' =>  7, 'rate' =>  3.9],
                        ['name' => 'Godsteel',              'qty' =>  8, 'rate' =>  7.8],
                        ['name' => 'Godsteel',              'qty' =>  9, 'rate' => 27.2],
                        ['name' => 'Godsteel',              'qty' => 10, 'rate' => 31.1],
                        ['name' => 'Godsteel',              'qty' => 11, 'rate' => 15.6],
                        ['name' => 'Godsteel',              'qty' => 12, 'rate' =>  7.8],
                        ['name' => 'Godsteel',              'qty' => 13, 'rate' =>  3.9],
                        ['name' => 'Godsteel',              'qty' => 14, 'rate' =>  2.0],
                        ['name' => 'Godsteel',              'qty' => 15, 'rate' =>  0.8],
                        ['name' => 'Weapon',                'qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Weapon Reroll Stone',   'qty' =>  1, 'rate' =>  5.1],
                        ['name' => 'Ancestral Aetherstones','qty' =>  1, 'rate' =>  0.5],
                        ['name' => 'Whetstone',             'qty' => 40, 'rate' =>  2.5],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  4, 'rate' =>  1.9],
                        ['name' => 'Mortal Aetherstones',   'qty' =>  5, 'rate' =>  1.9],
                    ]],
                ],
            ],
            'passives' => [
                ['name' => 'Immortal Powers',   'desc' => 'Reduces all damage by 90% while at least one Totem is alive. Reflects all Disable effects onto the Totems.', 'order' => 1, 'img' => '/images/bosses/abilities/icon-gwrach-yrhibyn-immortal-passive.png'],
                ['name' => 'Totemic Renewal',   'desc' => 'Resurrects all Totems at the start of each turn.',                                                           'order' => 2, 'img' => '/images/bosses/abilities/icon-gwrach-yrhibyn-totemic-passive.png'],
                ['name' => 'Mythic Spirit',      'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                                                      'order' => 3, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',       'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                                                           'order' => 4, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',              'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.',                             'order' => 5, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',  'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to Lock and Charm.',                                   'order' => 6, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [],
        ],

        // ══════════════════════════════════════════════
        // FAFNIR
        // ══════════════════════════════════════════════

        [
            'name'        => 'Fafnir',
            'imageUrl'    => '/images/bosses/art/fafnir.webp',
            'description' => 'Fafnir is a lair boss with a multi-phase kit, passive-heavy pressure, and ultimate windows that force careful tempo control.',
            'difficulty'  => 'Fafnir',
            'spell1Name'  => null, 'spell1Desc' => null, 'spell1Img' => null,
            'spell2Name'  => null, 'spell2Desc' => null, 'spell2Img' => null, 'spell2CD' => null,
            'spell3Name'  => null, 'spell3Desc' => null, 'spell3Img' => null, 'spell3CD' => null,
            'rewards'     => [
                ['name' => 'Gold',                   'qty' => '—',   'imageUrl' => '/images/rewards/ICON_Gold.png'],
                ['name' => 'Fafnir Cache',            'qty' => '1',   'imageUrl' => '/images/rewards/ICON_ChestReward.png'],
                ['name' => 'Mortal Aetherstone',      'qty' => '1–2', 'imageUrl' => '/images/rewards/ICON_MortalAetherstone.png'],
                ['name' => 'Ancestral Aetherstone',   'qty' => '1',   'imageUrl' => '/images/rewards/ICON_AncestralAetherstone.png'],
                ['name' => 'Weapon Crafting Material','qty' => '10',  'imageUrl' => '/images/rewards/ICON_WeaponCrafting.png'],
                ['name' => 'Lesser Ryvenite',         'qty' => '30',  'imageUrl' => '/images/rewards/ICON_LesserRyvenite.png'],
                ['name' => 'Greater Ryvenite',        'qty' => '6–7', 'imageUrl' => '/images/rewards/ICON_GreaterRyvenite.png'],
            ],
            'passives' => [
                ['name' => 'Mythic Spirit',      'desc' => 'Debuffs that reduce Divinity apply [Arcane Aegis I].',                         'order' => 1, 'img' => self::SHARED['mythic-spirit']],
                ['name' => 'Mythic Tempo',        'desc' => 'Turn meter reductions apply [Temporal Aegis I].',                             'order' => 2, 'img' => self::SHARED['mythic-tempo']],
                ['name' => 'Wrath',               'desc' => 'After 5 ultimates, deal 200% extra damage. Immune to Divinity cost increases.','order' => 3, 'img' => self::SHARED['wrath']],
                ['name' => 'Mythic Resilience',   'desc' => 'Reduces Acid, Blaze and Bleed damage by 75%. Immune to all Disables.',        'order' => 4, 'img' => self::SHARED['mythic-resilience']],
            ],
            'phases' => [
                [
                    'name'   => 'Hardened Dragon',
                    'order'  => 1,
                    'spell1' => ['name' => 'Burned to the Core', 'desc' => 'Attack all enemies (150% ATK) 2 times. Each hit activates [Blaze] on all enemies.',                                                        'img' => '/images/bosses/abilities/icon-fafnir-basic-phase1.png'],
                    'spell2' => ['name' => 'Dragonfire',         'desc' => 'Attack all enemies (300% ATK). Place [Blaze III] on all enemies for 3 turns.', 'cd' => 3,                                                 'img' => '/images/bosses/abilities/icon-fafnir-core-phase1.png'],
                    'spell3' => ['name' => 'Flames of Fafnir',   'desc' => 'Attack all enemies (400% ATK). Continuously activate [Blaze] on all enemies. Heal self for 30% HP. Costs 1000 Divinity.', 'cd' => null,  'img' => '/images/bosses/abilities/icon-fafnir-ultimate-phase1.png'],
                    'passives' => [
                        ['name' => 'Hardened Scales', 'desc' => 'Starts with 5 stacks, each increasing damage dealt by 3%. Stacks are reduced by hits and freeze effects.',       'order' => 1, 'img' => '/images/bosses/abilities/icon-fafnir-hardened-scales-passive.png'],
                        ['name' => 'Burning Breath',  'desc' => '50% chance to apply [Vulnerable III] before attacking. Deals 50% extra damage against [Blaze] targets.',         'order' => 2, 'img' => '/images/bosses/abilities/icon-fafnir-burning-breath-passive.png'],
                    ],
                ],
                [
                    'name'   => 'Tyrant Unbound',
                    'order'  => 2,
                    'spell1' => ['name' => 'Eternal Being',    'desc' => 'Attack all enemies (200% ATK). Increase the duration of all [Debuff] effects on all enemies.',                                  'img' => '/images/bosses/abilities/icon-fafnir-basic-phase2.png'],
                    'spell2' => ['name' => 'Burning Embers',   'desc' => 'Attack all enemies (200% ATK). Transfer all [Debuff] effects from Fafnir onto each enemy.', 'cd' => 3,                         'img' => '/images/bosses/abilities/icon-fafnir-core-phase2.png'],
                    'spell3' => ['name' => 'Tyrant\'s Hunger', 'desc' => 'Attack all enemies (300% ATK). Deals bonus damage against [Vulnerable] targets. Costs 1000 Divinity.', 'cd' => null,          'img' => '/images/bosses/abilities/icon-fafnir-ultimate-phase2.png'],
                    'passives' => [
                        ['name' => 'Weakened Scales', 'desc' => 'Accumulates up to 100 stacks, each increasing damage received by 1% per stack.',                                           'order' => 1, 'img' => '/images/bosses/abilities/icon-fafnir-weakened-scales-phase2.png'],
                        ['name' => 'Venomous Breath', 'desc' => '50% chance to apply [Acid] and [Vulnerable III] before attacking. Heals 5% HP when attacking enemies with debuffs.',      'order' => 2, 'img' => '/images/bosses/abilities/icon-fafnir-venomous-breath-passive.png'],
                    ],
                ],
            ],
        ],

    ];

    public function __construct(
        private EntityManagerInterface $em,
        private DungeonsRepository $repo,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Supprime tous les donjons existants avant de reseeder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seed Dungeons — 9 boss (Ascension, Armor, Fafnir)');

        if ($input->getOption('force')) {
            $existing = $this->repo->findAll();
            foreach ($existing as $d) {
                $this->em->remove($d);
            }
            $this->em->flush();
            $io->writeln(sprintf('  <comment>🗑</comment> %d donjons supprimés (--force)', count($existing)));
        }

        $created = 0;
        $skipped = 0;

        foreach (self::DUNGEONS as $data) {
            if ($this->repo->findOneBy(['name' => $data['name']])) {
                $io->writeln(sprintf('  <comment>~</comment> %s [déjà présent]', $data['name']));
                $skipped++;
                continue;
            }

            $dungeon = new Dungeons();
            $dungeon->setName($data['name']);
            $dungeon->setImageUrl(self::BASE . $data['imageUrl']);
            $dungeon->setDescription($data['description']);
            $dungeon->setDifficulty($data['difficulty']);

            if ($data['spell1Name']) {
                $dungeon->setSpell1Name($data['spell1Name']);
                $dungeon->setSpell1Description($data['spell1Desc']);
                $dungeon->setSpell1ImageUrl($data['spell1Img'] ? self::BASE . $data['spell1Img'] : null);
            }
            if ($data['spell2Name']) {
                $dungeon->setSpell2Name($data['spell2Name']);
                $dungeon->setSpell2Description($data['spell2Desc']);
                $dungeon->setSpell2Cooldown($data['spell2CD']);
                $dungeon->setSpell2ImageUrl($data['spell2Img'] ? self::BASE . $data['spell2Img'] : null);
            }
            if ($data['spell3Name']) {
                $dungeon->setSpell3Name($data['spell3Name']);
                $dungeon->setSpell3Description($data['spell3Desc']);
                $dungeon->setSpell3Cooldown($data['spell3CD']);
                $dungeon->setSpell3ImageUrl($data['spell3Img'] ? self::BASE . $data['spell3Img'] : null);
            }

            if (!empty($data['rewards'])) {
                $rewards = $data['rewards'];
                if (isset($rewards['stages']) || isset($rewards['gearPool'])) {
                    // New per-stage format — inject reward icons into drops
                    if (isset($rewards['stages'])) {
                        foreach ($rewards['stages'] as &$stage) {
                            if (isset($stage['drops'])) {
                                foreach ($stage['drops'] as &$drop) {
                                    if (empty($drop['imageUrl']) && isset(self::REWARD_ICONS[$drop['name']])) {
                                        $drop['imageUrl'] = self::REWARD_ICONS[$drop['name']];
                                    }
                                }
                                unset($drop);
                            }
                        }
                        unset($stage);
                    }
                    $dungeon->setRewardsJson(json_encode($rewards));
                } else {
                    // Legacy flat format — prefix relative imageUrls
                    $rewards = array_map(function ($r) {
                        $url = $r['imageUrl'] ?? '';
                        if ($url !== '' && !str_starts_with($url, 'http')) {
                            $url = self::BASE . $url;
                        }
                        return array_merge($r, ['imageUrl' => $url]);
                    }, $rewards);
                    $dungeon->setRewardsJson(json_encode($rewards));
                }
            }

            foreach ($data['passives'] as $p) {
                $passive = new DungeonPassive();
                $passive->setName($p['name']);
                $passive->setDescription($p['desc']);
                $passive->setPassiveOrder($p['order']);
                $passive->setImageUrl($p['img'] ? self::BASE . $p['img'] : null);
                $dungeon->addPassive($passive);
            }

            foreach ($data['phases'] as $phData) {
                $phase = new DungeonPhase();
                $phase->setName($phData['name']);
                $phase->setOrderNum($phData['order']);

                if (!empty($phData['spell1'])) {
                    $phase->setSpell1NameOverride($phData['spell1']['name']);
                    $phase->setSpell1DescriptionOverride($phData['spell1']['desc']);
                    $phase->setSpell1ImageOverride(isset($phData['spell1']['img']) ? self::BASE . $phData['spell1']['img'] : null);
                }
                if (!empty($phData['spell2'])) {
                    $phase->setSpell2NameOverride($phData['spell2']['name']);
                    $phase->setSpell2DescriptionOverride($phData['spell2']['desc']);
                    $phase->setSpell2CooldownOverride($phData['spell2']['cd'] ?? null);
                    $phase->setSpell2ImageOverride(isset($phData['spell2']['img']) ? self::BASE . $phData['spell2']['img'] : null);
                }
                if (!empty($phData['spell3'])) {
                    $phase->setSpell3NameOverride($phData['spell3']['name']);
                    $phase->setSpell3DescriptionOverride($phData['spell3']['desc']);
                    $phase->setSpell3CooldownOverride($phData['spell3']['cd'] ?? null);
                    $phase->setSpell3ImageOverride(isset($phData['spell3']['img']) ? self::BASE . $phData['spell3']['img'] : null);
                }

                foreach ($phData['passives'] as $pp) {
                    $passive = new DungeonPassive();
                    $passive->setName($pp['name']);
                    $passive->setDescription($pp['desc']);
                    $passive->setPassiveOrder($pp['order']);
                    $passive->setImageUrl($pp['img'] ? self::BASE . $pp['img'] : null);
                    $phase->addPassive($passive);
                }

                $dungeon->addPhase($phase);
            }

            $this->em->persist($dungeon);
            $created++;
            $io->writeln(sprintf('  <info>+</info> %s (%s)', $data['name'], $data['difficulty']));
        }

        $this->em->flush();
        $io->success(sprintf('%d donjons créés, %d déjà existants', $created, $skipped));

        return Command::SUCCESS;
    }
}
