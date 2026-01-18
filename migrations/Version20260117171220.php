<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117171220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_upgrade (id INT AUTO_INCREMENT NOT NULL, skill_type VARCHAR(20) NOT NULL, upgrade_level INT NOT NULL, effect_description VARCHAR(255) NOT NULL, hero_id INT NOT NULL, INDEX IDX_AD93EA3345B0BCD (hero_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE skill_upgrade ADD CONSTRAINT FK_AD93EA3345B0BCD FOREIGN KEY (hero_id) REFERENCES heroes (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skill_upgrade DROP FOREIGN KEY FK_AD93EA3345B0BCD');
        $this->addSql('DROP TABLE skill_upgrade');
    }
}
