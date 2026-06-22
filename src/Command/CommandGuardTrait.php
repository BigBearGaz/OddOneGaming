<?php

namespace App\Command;

trait CommandGuardTrait
{
    /** @var resource|false|null */
    private $lockHandle = null;

    /**
     * Acquiert un verrou exclusif via flock() pour bloquer les exécutions concurrentes.
     * Retourne false si la commande tourne déjà.
     */
    private function acquireLock(string $commandName): bool
    {
        $lockFile = sys_get_temp_dir() . '/oddone_cmd_' . str_replace(':', '_', $commandName) . '.lock';
        $this->lockHandle = fopen($lockFile, 'w');

        if (!$this->lockHandle || !flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
            return false;
        }

        // Le verrou est libéré automatiquement à la fin du process PHP
        return true;
    }

    /**
     * Fixe la limite mémoire et désactive le timeout PHP pour les commandes longues.
     */
    private function applyResourceLimits(int $memoryMb = 512): void
    {
        ini_set('memory_limit', $memoryMb . 'M');
        set_time_limit(0);
    }
}
