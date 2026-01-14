<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114064210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE effects ADD level1 LONGTEXT DEFAULT NULL, ADD level2 LONGTEXT DEFAULT NULL, ADD level3 LONGTEXT DEFAULT NULL, DROP level_1, DROP level_2, DROP level_3, CHANGE type type VARCHAR(20) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE effects ADD level_1 TEXT DEFAULT NULL, ADD level_2 TEXT DEFAULT NULL, ADD level_3 TEXT DEFAULT NULL, DROP level1, DROP level2, DROP level3, CHANGE type type ENUM(\'buff\', \'debuff\', \'disable\') NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
    }
}
