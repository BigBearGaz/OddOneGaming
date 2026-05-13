<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513121328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE heroes ADD stat_hp INT DEFAULT NULL, ADD stat_atk INT DEFAULT NULL, ADD stat_def INT DEFAULT NULL, ADD stat_spd INT DEFAULT NULL, ADD stat_init INT DEFAULT NULL, ADD stat_acc INT DEFAULT NULL, ADD stat_res INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE heroes DROP stat_hp, DROP stat_atk, DROP stat_def, DROP stat_spd, DROP stat_init, DROP stat_acc, DROP stat_res');
    }
}
