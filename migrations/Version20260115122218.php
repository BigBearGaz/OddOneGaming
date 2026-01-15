<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115122218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dungeons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, spell1_name VARCHAR(255) DEFAULT NULL, spell1 LONGTEXT DEFAULT NULL, spell2_name VARCHAR(255) DEFAULT NULL, spell2 LONGTEXT DEFAULT NULL, spell2_cooldown INT DEFAULT NULL, spell3_name VARCHAR(255) DEFAULT NULL, spell3 LONGTEXT DEFAULT NULL, spell3_cooldown INT DEFAULT NULL, passif LONGTEXT DEFAULT NULL, passif2 LONGTEXT DEFAULT NULL, passif3 LONGTEXT DEFAULT NULL, passif4 LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE effects (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(20) NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, icon_url_level1 VARCHAR(255) DEFAULT NULL, icon_url_level2 VARCHAR(255) DEFAULT NULL, icon_url_level3 VARCHAR(255) DEFAULT NULL, level1 LONGTEXT DEFAULT NULL, level2 LONGTEXT DEFAULT NULL, level3 LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, faction VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, allegiance VARCHAR(255) DEFAULT NULL, base VARCHAR(255) DEFAULT NULL, core VARCHAR(255) DEFAULT NULL, ultimate VARCHAR(255) DEFAULT NULL, passive VARCHAR(255) DEFAULT NULL, imprint VARCHAR(255) DEFAULT NULL, affinity VARCHAR(255) DEFAULT NULL, leader VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, weapons1 VARCHAR(255) DEFAULT NULL, weapons2 VARCHAR(255) DEFAULT NULL, imprint1 VARCHAR(255) DEFAULT NULL, imprint2 VARCHAR(255) DEFAULT NULL, imprint3 VARCHAR(255) NOT NULL, videos_url VARCHAR(500) DEFAULT NULL, debuffs VARCHAR(255) DEFAULT NULL, buffs VARCHAR(255) DEFAULT NULL, disable VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sets (id INT AUTO_INCREMENT NOT NULL, two VARCHAR(255) DEFAULT NULL, four VARCHAR(255) DEFAULT NULL, six VARCHAR(255) DEFAULT NULL, helmet VARCHAR(255) DEFAULT NULL, pauldrons VARCHAR(255) DEFAULT NULL, chest VARCHAR(255) DEFAULT NULL, gauntlets VARCHAR(255) DEFAULT NULL, legs VARCHAR(255) DEFAULT NULL, boots VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE takeovers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, details VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dungeons');
        $this->addSql('DROP TABLE effects');
        $this->addSql('DROP TABLE heroes');
        $this->addSql('DROP TABLE sets');
        $this->addSql('DROP TABLE takeovers');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
