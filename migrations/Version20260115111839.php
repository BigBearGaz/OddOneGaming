<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115111839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dungeons CHANGE spell1 spell1 LONGTEXT DEFAULT NULL, CHANGE spell2 spell2 LONGTEXT DEFAULT NULL, CHANGE spell3 spell3 LONGTEXT DEFAULT NULL, CHANGE passif passif LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dungeons CHANGE spell1 spell1 TEXT DEFAULT NULL, CHANGE spell2 spell2 TEXT DEFAULT NULL, CHANGE spell3 spell3 TEXT DEFAULT NULL, CHANGE passif passif TEXT DEFAULT NULL');
    }
}
