<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SkillExtension extends AbstractExtension
{
   private const EFFECTS = [

   
    
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
];


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
