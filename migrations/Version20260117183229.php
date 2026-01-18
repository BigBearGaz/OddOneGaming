<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117183229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skill_upgrade ADD level1 LONGTEXT DEFAULT NULL, ADD level2 LONGTEXT DEFAULT NULL, ADD level3 LONGTEXT DEFAULT NULL, ADD level4 LONGTEXT DEFAULT NULL, ADD level5 LONGTEXT DEFAULT NULL, ADD level6 LONGTEXT DEFAULT NULL, DROP upgrade_levels');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skill_upgrade ADD upgrade_levels JSON NOT NULL, DROP level1, DROP level2, DROP level3, DROP level4, DROP level5, DROP level6');
    }
}
