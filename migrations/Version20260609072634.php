<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260609072634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tier_list_mode (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, group_name VARCHAR(50) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, sort_order INT NOT NULL, UNIQUE INDEX UNIQ_C1B3342C989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE hero_tier_list MODIFY category VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tier_list_mode');
    }
}
