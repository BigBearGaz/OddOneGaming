<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260118110006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boss_phase (id INT AUTO_INCREMENT NOT NULL, phase_name VARCHAR(100) NOT NULL, phase_order INT NOT NULL, spell1_override LONGTEXT DEFAULT NULL, spell2_override LONGTEXT DEFAULT NULL, spell3_override LONGTEXT DEFAULT NULL, boss_id INT NOT NULL, INDEX IDX_C05A357F261FB672 (boss_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE boss_phase_passive (id INT AUTO_INCREMENT NOT NULL, passive_name VARCHAR(255) NOT NULL, passive_description LONGTEXT NOT NULL, passive_order INT NOT NULL, boss_phase_id INT NOT NULL, INDEX IDX_C98CD631106390C1 (boss_phase_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dungeon_boss (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, element VARCHAR(100) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, base_hp INT DEFAULT NULL, base_atk INT DEFAULT NULL, base_def INT DEFAULT NULL, base_speed INT DEFAULT NULL, initial_divinity INT DEFAULT NULL, divinity_cost INT DEFAULT NULL, spell1_name VARCHAR(255) DEFAULT NULL, spell1_description LONGTEXT DEFAULT NULL, spell2_name VARCHAR(255) DEFAULT NULL, spell2_description LONGTEXT DEFAULT NULL, spell2_cooldown INT DEFAULT NULL, spell3_name VARCHAR(255) DEFAULT NULL, spell3_description LONGTEXT DEFAULT NULL, spell3_cooldown INT DEFAULT NULL, dungeon_id INT NOT NULL, UNIQUE INDEX UNIQ_5A2AAAC2B606863 (dungeon_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE boss_phase ADD CONSTRAINT FK_C05A357F261FB672 FOREIGN KEY (boss_id) REFERENCES dungeon_boss (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boss_phase_passive ADD CONSTRAINT FK_C98CD631106390C1 FOREIGN KEY (boss_phase_id) REFERENCES boss_phase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_boss ADD CONSTRAINT FK_5A2AAAC2B606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeons ADD description LONGTEXT DEFAULT NULL, ADD difficulty VARCHAR(50) DEFAULT NULL, DROP spell1_name, DROP spell1, DROP spell2_name, DROP spell2, DROP spell2_cooldown, DROP spell3_name, DROP spell3, DROP spell3_cooldown, DROP passif, DROP passif2, DROP passif3, DROP passif4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boss_phase DROP FOREIGN KEY FK_C05A357F261FB672');
        $this->addSql('ALTER TABLE boss_phase_passive DROP FOREIGN KEY FK_C98CD631106390C1');
        $this->addSql('ALTER TABLE dungeon_boss DROP FOREIGN KEY FK_5A2AAAC2B606863');
        $this->addSql('DROP TABLE boss_phase');
        $this->addSql('DROP TABLE boss_phase_passive');
        $this->addSql('DROP TABLE dungeon_boss');
        $this->addSql('ALTER TABLE dungeons ADD spell1_name VARCHAR(255) DEFAULT NULL, ADD spell2_name VARCHAR(255) DEFAULT NULL, ADD spell2 LONGTEXT DEFAULT NULL, ADD spell2_cooldown INT DEFAULT NULL, ADD spell3_name VARCHAR(255) DEFAULT NULL, ADD spell3 LONGTEXT DEFAULT NULL, ADD spell3_cooldown INT DEFAULT NULL, ADD passif LONGTEXT DEFAULT NULL, ADD passif2 LONGTEXT DEFAULT NULL, ADD passif3 LONGTEXT DEFAULT NULL, ADD passif4 LONGTEXT DEFAULT NULL, DROP difficulty, CHANGE description spell1 LONGTEXT DEFAULT NULL');
    }
}
