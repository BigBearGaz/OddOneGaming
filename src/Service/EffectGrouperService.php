<?php

namespace App\Service;

use App\Twig\SkillExtension;
use Doctrine\DBAL\Connection;

class EffectGrouperService
{
    public function __construct(private readonly Connection $connection) {}

    public function getGroups(string $type): array
    {
        [$joinTable, $fkCol, $nameTable] = match ($type) {
            'buff'    => ['heroes_buffs',   'buffs_id',   'buffs'],
            'debuff'  => ['heroes_debuffs', 'debuffs_id', 'debuffs'],
            'disable' => ['heroes_disable', 'disable_id', 'disable'],
        };

        $counts   = $this->connection->fetchAllKeyValue(
            "SELECT {$fkCol}, COUNT(heroes_id) FROM {$joinTable} GROUP BY {$fkCol}"
        );
        $nameToId = $this->connection->fetchAllKeyValue("SELECT name, id FROM {$nameTable}");

        $rows   = $this->connection->fetchAllAssociative("SELECT name, description, icon_url FROM {$nameTable}");
        $dbRows = array_column($rows, null, 'name');

        return $this->buildGroups($type, $nameToId, $counts, $dbRows);
    }

    private function buildGroups(string $type, array $nameToId, array $countById, array $dbRows = []): array
    {
        $groups = [];
        $romanPattern = '/\s+(I{1,3}|IV|VI{0,3}|IX)$/i';
        $skip = ['Buff', 'Debuff', 'Disable'];

        foreach (SkillExtension::EFFECTS as $name => $data) {
            if ($data['type'] !== $type || in_array($name, $skip, true)) {
                continue;
            }

            $baseName = preg_replace($romanPattern, '', $name);
            $tier     = strtoupper(trim(substr($name, strlen($baseName)))) ?: null;

            $dbRow   = $dbRows[$name] ?? null;
            $iconUrl = ($dbRow['icon_url'] ?? null) ?: SkillExtension::iconUrl($name);
            $desc    = ($dbRow['description'] ?? null) ?: $data['desc'];

            $id    = $nameToId[$name] ?? null;
            $count = $id !== null ? (int) ($countById[$id] ?? 0) : 0;

            if (!isset($groups[$baseName])) {
                $groups[$baseName] = [
                    'name'        => $baseName,
                    'iconUrl'     => $iconUrl,
                    'description' => $tier ? null : $desc,
                    'tiers'       => [],
                    'heroCount'   => 0,
                ];
            }

            if ($tier) {
                $groups[$baseName]['tiers'][$tier] = $desc;
                if ($tier === 'I' || !$groups[$baseName]['description']) {
                    $groups[$baseName]['description'] = $desc;
                }
            }

            $groups[$baseName]['heroCount'] += $count;

            if (!$groups[$baseName]['iconUrl'] && $iconUrl) {
                $groups[$baseName]['iconUrl'] = $iconUrl;
            }
        }

        ksort($groups);
        return array_values($groups);
    }
}
