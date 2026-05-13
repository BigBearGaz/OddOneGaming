<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SkillExtension extends AbstractExtension
{
   public const EFFECTS = [

   
    
    // 🟢 BUFFS
    'Buff'     => ['type' => 'buff',     'desc' => 'Improves ally/ies: +stats, protection, healing'],
    'ACC Up I'   => ['type' => 'buff',  'desc' => 'Increases ACC stat 20%'],
    'ACC Up II'  => ['type' => 'buff',  'desc' => 'Increases ACC stat 40%'],
    'ACC Up III' => ['type' => 'buff',  'desc' => 'Increases ACC stat 60%'],
    'ATK Up I'   => ['type' => 'buff',  'desc' => 'Increases Hero Battle ATK stat 20%'],
    'ATK Up II'  => ['type' => 'buff',  'desc' => 'Increases Hero Battle ATK stat 40%'],
    'ATK Up III' => ['type' => 'buff',  'desc' => 'Increases Hero Battle ATK stat 60%'],
    'Barrier'    => ['type' => 'buff',  'desc' => 'Absorbs incoming damage equal to the strength of the barrier before depleting HP. Barriers can stack and be increased.'],
    'DEF Up I'   => ['type' => 'buff',  'desc' => 'Increases DEF stat 20%'],
    'DEF Up II'  => ['type' => 'buff',  'desc' => 'Increases DEF stat 40%'],
    'DEF Up III' => ['type' => 'buff',  'desc' => 'Increases DEF stat 60%'],
    'Immortal'   => ['type' => 'buff',  'desc' => 'HP cannot be reduced below 1. Duration reduced when triggered.'],
    'INIT Up I'  => ['type' => 'buff',  'desc' => 'Increases INIT stat 10%'],
    'INIT Up II' => ['type' => 'buff',  'desc' => 'Increases INIT stat 20%'],
    'INIT Up III'=> ['type' => 'buff',  'desc' => 'Increases INIT stat 30%'],
    'Intercept'  => ['type' => 'buff',  'desc' => 'Redirects an attack to the hero that placed Intercept. Duration is reduced when triggered.'],
    'Mend'       => ['type' => 'buff',  'desc' => 'Reduce Bleed Stacks by 1 and Heal 10% max HP at the start of the turn.'],
    'Phoenix'    => ['type' => 'buff',  'desc' => 'Instantly revive upon death with 100% HP and 100% turn meter.'],
    'Protect I'  => ['type' => 'buff',  'desc' => 'Ally who placed this buff takes 15% of the damage this unit would receive instead.'],
    'Protect II' => ['type' => 'buff',  'desc' => 'Ally who placed this buff takes 30% of the damage this unit would receive instead.'],
    'Protect III'=> ['type' => 'buff',  'desc' => 'Ally who placed this buff takes 45% of the damage this unit would receive instead.'],
    'Radiance I' => ['type' => 'buff',  'desc' => 'Reduces and reflects 10% of attack damage received.'],
    'Radiance II'=> ['type' => 'buff',  'desc' => 'Reduces and reflects 20% of attack damage received.'],
    'Radiance III'=> ['type' => 'buff', 'desc' => 'Reduces and reflects 30% of attack damage received.'],
    'Reflect'    => ['type' => 'buff',  'desc' => 'Reflect the next non-resisted debuff received. Duration reduced when triggered.'],
    'RES Up I'   => ['type' => 'buff',  'desc' => 'Increases RES stat 20%'],
    'RES Up II'  => ['type' => 'buff',  'desc' => 'Increases RES stat 40%'],
    'RES Up III' => ['type' => 'buff',  'desc' => 'Increases RES stat 60%'],
    'Retaliate'  => ['type' => 'buff',  'desc' => 'Counterattack enemy attackers when hit'],
    'Sharpen I'  => ['type' => 'buff',  'desc' => 'Raises Crit Damage stat and strong hit damage by 5%'],
    'Sharpen II' => ['type' => 'buff',  'desc' => 'Raises Crit Damage stat and strong hit damage by 10%'],
    'Sharpen III'=> ['type' => 'buff',  'desc' => 'Raises Crit Damage stat and strong hit damage by 15%'],
    'Shield'     => ['type' => 'buff',  'desc' => 'Absorbs incoming damage equal to the strength of the Shield before depleting HP. Shields cannot stack or be increased.'],
    'SPD Up I'   => ['type' => 'buff',  'desc' => 'Increases SPD stat 10%'],
    'SPD Up II'  => ['type' => 'buff',  'desc' => 'Increases SPD stat 20%'],
    'SPD Up III' => ['type' => 'buff',  'desc' => 'Increases SPD stat 30%'],
    'Stalwart'   => ['type' => 'buff',  'desc' => 'Immune to Disable. Duration is reduced when receiving a disable. Removes a disable on initial placement.'],
    'Vanish'     => ['type' => 'buff',  'desc' => 'Cannot be directly targeted.'],
    'Ward'       => ['type' => 'buff',  'desc' => 'Immune to attack damage. Duration reduced when triggered.'],
    'Arcane Aegis I'    => ['type' => 'buff', 'desc' => 'Reduces Divinity Reductions by 5%.'],
    'Arcane Aegis II'   => ['type' => 'buff', 'desc' => 'Reduces Divinity Reductions by 10%.'],
    'Arcane Aegis III'  => ['type' => 'buff', 'desc' => 'Reduces Divinity Reductions by 15%.'],
    'Backfire'          => ['type' => 'buff', 'desc' => 'When this hero would gain a non-resisted Debuff, the debuff is instead returned to the attacker. Duration reduced when triggered.'],
    'Block Debuffs'     => ['type' => 'buff', 'desc' => 'Blocks the next incoming Debuff. Duration reduced when triggered.'],
    'Faith'             => ['type' => 'buff', 'desc' => 'Increases FTH stat.'],
    'FTH Up I'          => ['type' => 'buff', 'desc' => 'Increases FTH stat 10%.'],
    'FTH Up II'         => ['type' => 'buff', 'desc' => 'Increases FTH stat 20%.'],
    'FTH Up III'        => ['type' => 'buff', 'desc' => 'Increases FTH stat 30%.'],
    'Gluttony'          => ['type' => 'buff', 'desc' => 'Restores HP equal to a portion of the damage dealt.'],
    'Precision'         => ['type' => 'buff', 'desc' => 'Attacks cannot be evaded or weakened.'],
    'Ricochet'          => ['type' => 'buff', 'desc' => 'Attacks that target this hero are redirected to another random target.'],
    'Temporal Aegis I'  => ['type' => 'buff', 'desc' => 'Reduces incoming Turn Meter reductions by 5%.'],
    'Temporal Aegis II' => ['type' => 'buff', 'desc' => 'Reduces incoming Turn Meter reductions by 10%.'],
    'Temporal Aegis III'=> ['type' => 'buff', 'desc' => 'Reduces incoming Turn Meter reductions by 15%.'],

    // 🔴 DEBUFFS
    'Debuff'   => ['type' => 'debuff',   'desc' => 'Penalizes enemy: -stats, DoT damage, debuffs'], 
    'ACC Down I'   => ['type' => 'debuff', 'desc' => 'Decrease ACC stat 20%'],
    'ACC Down II'  => ['type' => 'debuff', 'desc' => 'Decrease ACC stat 40%'],
    'ACC Down III' => ['type' => 'debuff', 'desc' => 'Decrease ACC stat 60%'],
    'Acid'         => ['type' => 'debuff', 'desc' => 'Disables Armor Sets and Deals 5% Max HP Damage at the start of the turn. Deals double to Shield and Barrier'],
    'Aetherburn I' => ['type' => 'debuff', 'desc' => 'Increases Chance to Weak Hit by 5% and reduces Divinity by 3% on all heroes on the team.'],
    'Aetherburn II'=> ['type' => 'debuff', 'desc' => 'Increases Chance to Weak Hit by 10% and reduces Divinity by 6% on all heroes on the team.'],
    'Aetherburn III'=> ['type' => 'debuff','desc' => 'Increases Chance to Weak Hit by 15% and reduces Divinity by 9% on all heroes on the team.'],
    'ATK Down I'   => ['type' => 'debuff', 'desc' => 'Decreases ATK stat 20%'],
    'ATK Down II'  => ['type' => 'debuff', 'desc' => 'Decreases ATK stat 40%'],
    'ATK Down III' => ['type' => 'debuff', 'desc' => 'Decreases ATK stat 60%'],
    'Banish'       => ['type' => 'debuff', 'desc' => 'Enemies that die with this debuff cannot be revived whilst this is on. When a revive occurs, the duration of this debuff is reduced.'],
    'Blaze I'      => ['type' => 'debuff', 'desc' => 'Deals damage equal to 2% Enemy Max HP to all heroes on the team.'],
    'Blaze II'     => ['type' => 'debuff', 'desc' => 'Deals damage equal to 4% Enemy Max HP to all heroes on the team.'],
    'Blaze III'    => ['type' => 'debuff', 'desc' => 'Deals damage equal to 6% Enemy Max HP to all heroes on the team.'],
    'Bleed'        => ['type' => 'debuff', 'desc' => 'Deals an enemy 1% of Max HP Damage each turn. Stacks up to 10 times on subsequent application.'],
    'Block Revive' => ['type' => 'debuff', 'desc' => 'Enemies that die with this debuff cannot be revived.'],
    'Blunt I'      => ['type' => 'debuff', 'desc' => 'Decreases Strong Hit Damage and Critical Damage by 10%'],
    'Blunt II'     => ['type' => 'debuff', 'desc' => 'Decreases Strong Hit Damage and Critical Damage by 20%'],
    'Blunt III'    => ['type' => 'debuff', 'desc' => 'Decreases Strong Hit Damage and Critical Damage by 30%'],
    'Charm'        => ['type' => 'debuff', 'desc' => 'Takes the next attack for the enemy that placed this debuff. Duration reduced when triggered.'],
    'Corrupt'      => ['type' => 'debuff', 'desc' => 'The unit that placed this debuff steals any buffs received. Duration reduced when triggered.'],
    'Curse I'      => ['type' => 'debuff', 'desc' => '25% reduced incoming healing and shield/barrier strength.'],
    'Curse II'     => ['type' => 'debuff', 'desc' => '50% reduced incoming healing and shield/barrier strength.'],
    'Curse III'    => ['type' => 'debuff', 'desc' => '75% reduced incoming healing and shield/barrier strength.'],
    'DEF Down I'   => ['type' => 'debuff', 'desc' => 'Decreases DEF stat 20%'],
    'DEF Down II'  => ['type' => 'debuff', 'desc' => 'Decreases DEF stat 40%'],
    'DEF Down III' => ['type' => 'debuff', 'desc' => 'Decreases DEF stat 60%'],
    'Drain I'      => ['type' => 'debuff', 'desc' => 'Attackers deal 10% increased damage and heal for 10% of the damage inflicted'],
    'Drain II'     => ['type' => 'debuff', 'desc' => 'Attackers deal 20% increased damage and heal for 20% of the damage inflicted'],
    'Drain III'    => ['type' => 'debuff', 'desc' => 'Attackers deal 30% increased damage and heal for 30% of the damage inflicted'],
    'Hex I'        => ['type' => 'debuff', 'desc' => 'Allies also take 5% of Damage received. This is halved for AoE Damage.'],
    'Hex II'       => ['type' => 'debuff', 'desc' => 'Allies also take 10% of Damage received. This is halved for AoE Damage.'],
    'Hex III'      => ['type' => 'debuff', 'desc' => 'Allies also take 15% of Damage received. This is halved for AoE Damage.'],
    'INIT Down I'  => ['type' => 'debuff', 'desc' => 'Decreases INIT stat 10%'],
    'INIT Down II' => ['type' => 'debuff', 'desc' => 'Decreases INIT stat 20%'],
    'INIT Down III'=> ['type' => 'debuff', 'desc' => 'Decreases INIT stat 30%'],
    'Lock Core'    => ['type' => 'debuff', 'desc' => 'Core abilities cannot be used.'],
    'Lock Passive' => ['type' => 'debuff', 'desc' => 'Passive abilities cannot be used.'],
    'Lock Ultimate'=> ['type' => 'debuff', 'desc' => 'Ultimate abilities cannot be used.'],
    'RES Down I'   => ['type' => 'debuff', 'desc' => 'Decreases RES stat 20%'],
    'RES Down II'  => ['type' => 'debuff', 'desc' => 'Decreases RES stat 40%'],
    'RES Down III' => ['type' => 'debuff', 'desc' => 'Decreases RES stat 60%'],
    'SPD Down I'   => ['type' => 'debuff', 'desc' => 'Decreases SPD stat 10%'],
    'SPD Down II'  => ['type' => 'debuff', 'desc' => 'Decreases SPD stat 20%'],
    'SPD Down III' => ['type' => 'debuff', 'desc' => 'Decreases SPD stat 30%'],
    'Terrify'      => ['type' => 'debuff', 'desc' => 'Increases Divinity Cost by 50%.'], 
    'Vulnerable I' => ['type' => 'debuff', 'desc' => 'Increases damage dealt by debuffs by 20%.'],
    'Vulnerable II'=> ['type' => 'debuff', 'desc' => 'Increases damage dealt by debuffs by 40%.'],
    'Vulnerable III'=> ['type' => 'debuff','desc' => 'Increases damage dealt by debuffs by 60%.'],
    'Dark Tidings'  => ['type' => 'debuff', 'desc' => 'Each Stack decreases the damage inflicted by 5%. Stacks up to 10 times.'],
    'FTH Down I'    => ['type' => 'debuff', 'desc' => 'Decreases FTH stat 10%.'],
    'FTH Down II'   => ['type' => 'debuff', 'desc' => 'Decreases FTH stat 20%.'],
    'FTH Down III'  => ['type' => 'debuff', 'desc' => 'Decreases FTH stat 30%.'],

    // 🟣 DISABLES
    'Confuse'  => ['type' => 'disable', 'desc' => 'This unit attacks a random ally with its basic ability on its turn, doing 75% reduced damage.'],
    'Despair'  => ['type' => 'disable', 'desc' => 'Skips next turn. Cannot receive positive buffs or effects. Duration is reduced when attempt to receive a positive buff or effect.'],
    'Freeze'   => ['type' => 'disable', 'desc' => 'Skips next turn. Incoming Turn Meter Fill effects are reduced by 50%.'],
    'Morph'    => ['type' => 'disable', 'desc' => 'Transforms the hero into a creature - Wolf, Bear or Frog. Heroes retain their stats but only have 1 ability.'],
    'Petrify'  => ['type' => 'disable', 'desc' => 'Skips next turn. Increase Divinity Effects are Reduced by 50%'],
    'Sleep'    => ['type' => 'disable', 'desc' => 'Skips next turn. Incoming Damage from Attacks increases by 30%. Duration reduced when attacked (per ability).'],
    'Stun'     => ['type' => 'disable', 'desc' => 'Skips next turn.'],
    'Disable'  => ['type' => 'disable',  'desc' => 'Control: Stun, Sleep, Taunt, Morph, Freeze'],
    'Taunt'    => ['type' => 'disable', 'desc' => 'Attacks the unit that placed this disable with its basic ability, doing 25% reduced damage.'],
    'Shade'    => ['type' => 'disable', 'desc' => 'Permanently removes the hero from Ally Team Control. Allies cannot target or interact with this hero.'],
];

    private const BASE_ICON = 'https://www.ravenpyros.com/application/files/';

    public const EFFECT_ICONS = [
        // Buffs
        'ACC Up I'     => '2117/5280/4991/AccUpI.png',
        'ACC Up II'    => '8917/5280/4992/AccUpII.png',
        'ACC Up III'   => '3017/5280/4992/AccUpIII.png',
        'ATK Up I'     => '5817/5280/4992/AtkUpI.png',
        'ATK Up II'    => '8917/5280/4992/AtkUpII.png',
        'ATK Up III'   => '3017/5280/4993/AtkUpIII.png',
        'Barrier'      => '3017/5280/4993/Barrier.png',
        'DEF Up I'     => '8517/5280/4994/DefUpI.png',
        'DEF Up II'    => '2417/5280/4994/DefUpII.png',
        'DEF Up III'   => '9917/5280/4994/DefUpIII.png',
        'Immortal'     => '9017/5280/4995/Immortal.png',
        'INIT Up I'    => '9117/5280/4995/InitUpI.png',
        'INIT Up II'   => '7717/5280/4996/InitUpII.png',
        'INIT Up III'  => '8317/5280/4996/InitUpIII.png',
        'Intercept'    => '5017/5280/4996/Intercept.png',
        'Mend'         => '4317/6962/9887/ICON_Mend.png',
        'Phoenix'      => '1317/5280/4996/Phoenix.png',
        'Protect I'    => '6017/5280/4996/ProtectI.png',
        'Protect II'   => '7417/5280/4996/ProtectII.png',
        'Protect III'  => '5317/5280/4997/ProtectIII.png',
        'Radiance I'   => '6417/5280/4997/RadianceI.png',
        'Radiance II'  => '6317/5280/4997/RadianceII.png',
        'Radiance III' => '3017/5280/4997/RadianceIII.png',
        'RES Up I'     => '8517/5280/4997/ResUpI.png',
        'RES Up II'    => '5317/5280/4997/ResUpII.png',
        'RES Up III'   => '8617/5280/4997/ResUpIII.png',
        'Retaliate'    => '1917/5280/4998/Retaliate.png',
        'Sharpen I'    => '9817/5280/4998/SharpenI.png',
        'Sharpen II'   => '9917/5280/4998/SharpenII.png',
        'Sharpen III'  => '5717/5280/4998/SharpenIII.png',
        'Shield'       => '5017/5280/4998/Shield.png',
        'SPD Up I'     => '1617/5280/4998/SpdUpI.png',
        'SPD Up II'    => '3317/5280/4998/SpdUpII.png',
        'SPD Up III'   => '3517/5280/4998/SpdUpIII.png',
        'Stalwart'     => '9117/5280/4999/Stalwart.png',
        'Vanish'       => '7617/5280/4999/Vanish.png',
        'Ward'         => '4117/5280/4999/Ward.png',
        'Arcane Aegis I'    => '9117/6962/9381/ICON_ArcaneAegis.png',
        'Arcane Aegis II'   => '9117/6962/9381/ICON_ArcaneAegis.png',
        'Arcane Aegis III'  => '9117/6962/9381/ICON_ArcaneAegis.png',
        'Backfire'          => '7317/6962/9670/ICON_Backfire.png',
        'Block Debuffs'     => '7217/6962/9767/ICON_BlockDebuffs.png',
        'Gluttony'          => '4317/7613/5651/Gluttony.png',
        'Precision'         => '7717/6962/9979/ICON_Precision.png',
        'Ricochet'          => '4017/6963/1174/ICON_Ricochet.png',
        'Temporal Aegis I'  => '3017/6963/4453/ICON_TemporalAegis.png',
        'Temporal Aegis II' => '3017/6963/4453/ICON_TemporalAegis.png',
        'Temporal Aegis III'=> '3017/6963/4453/ICON_TemporalAegis.png',
        // Debuffs
        'ACC Down I'    => '8417/5280/4991/AccDownI.png',
        'ACC Down II'   => '1817/5280/4991/AccDownII.png',
        'ACC Down III'  => '6117/5280/4992/AccDownIII.png',
        'Acid'          => '6017/5280/4992/Acid.png',
        'Aetherburn I'  => '2417/6963/4967/ICON_Aetherburn1.png',
        'Aetherburn II' => '8617/6963/4967/ICON_Aetherburn2.png',
        'Aetherburn III'=> '4717/6963/4966/ICON_Aetherburn3.png',
        'ATK Down I'    => '6117/6963/6295/ICON_AtkDown1.png',
        'ATK Down II'   => '4417/6963/6294/ICON_AtkDown2.png',
        'ATK Down III'  => '2517/6963/6295/ICON_AtkDown3.png',
        'Banish'        => '8517/5280/4993/Banish.png',
        'Blaze I'       => '5417/5280/4993/BlazeI.png',
        'Blaze II'      => '8717/5280/4993/BlazeII.png',
        'Blaze III'     => '3617/5280/4993/BlazeIII.png',
        'Bleed'         => '3517/5280/4993/Bleed.png',
        'Block Revive'  => '9017/5280/4993/BlockRevive.png',
        'Blunt I'       => '5317/5280/4993/BluntI.png',
        'Blunt II'      => '5917/5280/4993/BluntII.png',
        'Blunt III'     => '6617/5280/4993/BluntIII.png',
        'Charm'         => '5717/5280/4993/Charm.png',
        'Corrupt'       => '2017/5280/4994/Corrupt.png',
        'Curse I'       => '5517/5280/4994/CurseI.png',
        'Curse II'      => '1317/5280/4994/CurseII.png',
        'Curse III'     => '4917/5280/4994/CurseIII.png',
        'DEF Down I'    => '8917/5280/4994/DefDownI.png',
        'DEF Down II'   => '8917/5280/4994/DefDownII.png',
        'DEF Down III'  => '6317/5280/4994/DefDownIII.png',
        'Drain I'       => '1317/5280/4995/DrainI.png',
        'Drain II'      => '1217/5280/4995/DrainII.png',
        'Drain III'     => '3217/5280/4995/DrainIII.png',
        'Hex I'         => '7217/5280/4995/HexI.png',
        'Hex II'        => '4717/5280/4995/HexII.png',
        'Hex III'       => '1217/5280/4995/HexIII.png',
        'INIT Down I'   => '7117/5280/4995/InitDownI.png',
        'INIT Down II'  => '4117/5280/4995/InitDownII.png',
        'INIT Down III' => '1217/5280/4995/InitDownIII.png',
        'Lock Core'     => '6417/5280/4996/LockCore.png',
        'Lock Passive'  => '8117/5280/4996/LockPassive.png',
        'Lock Ultimate' => '8217/5280/4996/LockUlt.png',
        'RES Down I'    => '6117/5280/4997/ResDownI.png',
        'RES Down II'   => '3817/5280/4997/ResDownII.png',
        'RES Down III'  => '9617/5280/4997/ResDownIII.png',
        'SPD Down I'    => '8317/5280/4998/SpdDownI.png',
        'SPD Down II'   => '9717/5280/4998/SpdDownII.png',
        'SPD Down III'  => '8117/5280/4998/SpdDownIII.png',
        'Terrify'       => '7117/5280/4999/Terrify.png',
        'Vulnerable I'  => '6117/5280/4999/VulnerableI.png',
        'Vulnerable II' => '6717/5280/4999/VulnerableII.png',
        'Vulnerable III'=> '4317/5280/4999/VulnerableIII.png',
        'Dark Tidings'  => '7717/6963/5463/ICON_DarkTidings.png',
        // Disables
        'Confuse' => '5717/5280/4994/Confuse.png',
        'Despair' => '6517/5280/4994/Despair.png',
        'Freeze'  => '5117/5280/4995/Freeze.png',
        'Petrify' => '5917/5280/4996/Petrify.png',
        'Sleep'   => '1317/5280/4998/Sleep.png',
        'Stun'    => '8517/5280/4999/Stun.png',
        'Taunt'   => '5317/5280/4999/Taunt.png',
        'Shade'   => '8117/6963/1663/ICON_Shade.png',
    ];

    public static function iconUrl(string $name): ?string
    {
        $path = self::EFFECT_ICONS[$name] ?? null;
        return $path ? self::BASE_ICON . $path : null;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('parse_effects', [$this, 'parseEffects'], ['is_safe' => ['html']]),
        ];
    }

    public function parseEffects(?string $text): string
    {
        if (!$text) {
            return '';
        }

        return preg_replace_callback('/\[([^\]]+)\]/', function ($matches) {
            $effectName = $matches[1];
            
            if (!isset(self::EFFECTS[$effectName])) {
                return $matches[0]; // Retourne le texte original si l'effet n'existe pas
            }

            $effect = self::EFFECTS[$effectName];
            $escapedDesc = htmlspecialchars($effect['desc'], ENT_QUOTES);
            $escapedName = htmlspecialchars($effectName, ENT_QUOTES);

            return sprintf(
                '<span class="skill-effect effect-%s" data-effect="%s" data-description="%s">[%s]</span>',
                $effect['type'],
                $escapedName,
                $escapedDesc,
                $escapedName
            );
        }, $text);
    }
}
