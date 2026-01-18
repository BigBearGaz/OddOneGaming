<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260118114016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dungeon_passive (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, passive_order INT NOT NULL, dungeon_id INT DEFAULT NULL, phase_id INT DEFAULT NULL, INDEX IDX_2BC9514CB606863 (dungeon_id), INDEX IDX_2BC9514C99091188 (phase_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dungeon_phase (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, order_num INT NOT NULL, spell1_name_override VARCHAR(255) DEFAULT NULL, spell1_description_override LONGTEXT DEFAULT NULL, spell2_name_override VARCHAR(255) DEFAULT NULL, spell2_description_override LONGTEXT DEFAULT NULL, spell2_cooldown_override INT DEFAULT NULL, spell3_name_override VARCHAR(255) DEFAULT NULL, spell3_description_override LONGTEXT DEFAULT NULL, spell3_cooldown_override INT DEFAULT NULL, dungeon_id INT NOT NULL, INDEX IDX_2BF7829B606863 (dungeon_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE dungeon_passive ADD CONSTRAINT FK_2BC9514CB606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_passive ADD CONSTRAINT FK_2BC9514C99091188 FOREIGN KEY (phase_id) REFERENCES dungeon_phase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_phase ADD CONSTRAINT FK_2BF7829B606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boss_phase DROP FOREIGN KEY `FK_C05A357F261FB672`');
        $this->addSql('ALTER TABLE boss_phase_passive DROP FOREIGN KEY `FK_C98CD631106390C1`');
        $this->addSql('ALTER TABLE dungeon_boss DROP FOREIGN KEY `FK_5A2AAAC2B606863`');
        $this->addSql('DROP TABLE boss_phase');
        $this->addSql('DROP TABLE boss_phase_passive');
        $this->addSql('DROP TABLE dungeon_boss');
        $this->addSql('ALTER TABLE dungeons ADD spell1_name VARCHAR(255) DEFAULT NULL, ADD spell1_description LONGTEXT DEFAULT NULL, ADD spell2_name VARCHAR(255) DEFAULT NULL, ADD spell2_description LONGTEXT DEFAULT NULL, ADD spell2_cooldown INT DEFAULT NULL, ADD spell3_name VARCHAR(255) DEFAULT NULL, ADD spell3_description LONGTEXT DEFAULT NULL, ADD spell3_cooldown INT DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boss_phase (id INT AUTO_INCREMENT NOT NULL, phase_name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, phase_order INT NOT NULL, spell1_override LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell2_override LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell3_override LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, boss_id INT NOT NULL, INDEX IDX_C05A357F261FB672 (boss_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE boss_phase_passive (id INT AUTO_INCREMENT NOT NULL, passive_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, passive_description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, passive_order INT NOT NULL, boss_phase_id INT NOT NULL, INDEX IDX_C98CD631106390C1 (boss_phase_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE dungeon_boss (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, element VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, image_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, base_hp INT DEFAULT NULL, base_atk INT DEFAULT NULL, base_def INT DEFAULT NULL, base_speed INT DEFAULT NULL, initial_divinity INT DEFAULT NULL, divinity_cost INT DEFAULT NULL, spell1_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell1_description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell2_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell2_description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell2_cooldown INT DEFAULT NULL, spell3_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell3_description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, spell3_cooldown INT DEFAULT NULL, dungeon_id INT NOT NULL, UNIQUE INDEX UNIQ_5A2AAAC2B606863 (dungeon_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE boss_phase ADD CONSTRAINT `FK_C05A357F261FB672` FOREIGN KEY (boss_id) REFERENCES dungeon_boss (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boss_phase_passive ADD CONSTRAINT `FK_C98CD631106390C1` FOREIGN KEY (boss_phase_id) REFERENCES boss_phase (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_boss ADD CONSTRAINT `FK_5A2AAAC2B606863` FOREIGN KEY (dungeon_id) REFERENCES dungeons (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_passive DROP FOREIGN KEY FK_2BC9514CB606863');
        $this->addSql('ALTER TABLE dungeon_passive DROP FOREIGN KEY FK_2BC9514C99091188');
        $this->addSql('ALTER TABLE dungeon_phase DROP FOREIGN KEY FK_2BF7829B606863');
        $this->addSql('DROP TABLE dungeon_passive');
        $this->addSql('DROP TABLE dungeon_phase');
        $this->addSql('ALTER TABLE dungeons DROP spell1_name, DROP spell1_description, DROP spell2_name, DROP spell2_description, DROP spell2_cooldown, DROP spell3_name, DROP spell3_description, DROP spell3_cooldown, CHANGE name name VARCHAR(255) DEFAULT NULL');
    }
}
