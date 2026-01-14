<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114093216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dungeons ADD spell1_name VARCHAR(255) DEFAULT NULL, ADD spell2_name VARCHAR(255) DEFAULT NULL, ADD spell2_cooldown INT DEFAULT NULL, ADD spell3_name VARCHAR(255) DEFAULT NULL, ADD spell3_cooldown INT DEFAULT NULL, ADD passif2 LONGTEXT DEFAULT NULL, ADD passif3 LONGTEXT DEFAULT NULL, ADD passif4 LONGTEXT DEFAULT NULL, CHANGE spell1 spell1 LONGTEXT DEFAULT NULL, CHANGE spell2 spell2 LONGTEXT DEFAULT NULL, CHANGE spell3 spell3 LONGTEXT DEFAULT NULL, CHANGE passif passif LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dungeons DROP spell1_name, DROP spell2_name, DROP spell2_cooldown, DROP spell3_name, DROP spell3_cooldown, DROP passif2, DROP passif3, DROP passif4, CHANGE spell1 spell1 VARCHAR(255) DEFAULT NULL, CHANGE spell2 spell2 VARCHAR(255) DEFAULT NULL, CHANGE spell3 spell3 VARCHAR(255) DEFAULT NULL, CHANGE passif passif VARCHAR(255) NOT NULL');
    }
}
