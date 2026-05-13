<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260423075349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affinity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE allegiance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE armor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slot VARCHAR(50) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, main_stat VARCHAR(50) DEFAULT NULL, sub_stat1 VARCHAR(50) DEFAULT NULL, sub_stat2 VARCHAR(50) DEFAULT NULL, sub_stat3 VARCHAR(50) DEFAULT NULL, sub_stat4 VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE awakening (id INT AUTO_INCREMENT NOT NULL, skill_type VARCHAR(20) NOT NULL, awakening_level INT NOT NULL, effect_description LONGTEXT NOT NULL, hero_id INT NOT NULL, INDEX IDX_A1F39B7845B0BCD (hero_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE buffs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE debuffs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE disable (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dungeon_passive (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, passive_order INT NOT NULL, dungeon_id INT DEFAULT NULL, phase_id INT DEFAULT NULL, INDEX IDX_2BC9514CB606863 (dungeon_id), INDEX IDX_2BC9514C99091188 (phase_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dungeon_phase (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, order_num INT NOT NULL, spell1_name_override VARCHAR(255) DEFAULT NULL, spell1_description_override LONGTEXT DEFAULT NULL, spell2_name_override VARCHAR(255) DEFAULT NULL, spell2_description_override LONGTEXT DEFAULT NULL, spell2_cooldown_override INT DEFAULT NULL, spell3_name_override VARCHAR(255) DEFAULT NULL, spell3_description_override LONGTEXT DEFAULT NULL, spell3_cooldown_override INT DEFAULT NULL, dungeon_id INT NOT NULL, INDEX IDX_2BF7829B606863 (dungeon_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dungeons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, difficulty VARCHAR(50) DEFAULT NULL, spell1_name VARCHAR(255) DEFAULT NULL, spell1_description LONGTEXT DEFAULT NULL, spell2_name VARCHAR(255) DEFAULT NULL, spell2_description LONGTEXT DEFAULT NULL, spell2_cooldown INT DEFAULT NULL, spell3_name VARCHAR(255) DEFAULT NULL, spell3_description LONGTEXT DEFAULT NULL, spell3_cooldown INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE faction (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, leader_value VARCHAR(255) DEFAULT NULL, base LONGTEXT DEFAULT NULL, core LONGTEXT DEFAULT NULL, ultimate LONGTEXT DEFAULT NULL, passive LONGTEXT DEFAULT NULL, imprint LONGTEXT DEFAULT NULL, image_url VARCHAR(500) DEFAULT NULL, videos_url VARCHAR(500) DEFAULT NULL, awakening_bonuses LONGTEXT DEFAULT NULL, ascension_bonuses LONGTEXT DEFAULT NULL, divinity_cost VARCHAR(255) DEFAULT NULL, initial_divinity VARCHAR(255) DEFAULT NULL, faction_entity_id INT DEFAULT NULL, type_entity_id INT DEFAULT NULL, allegiance_entity_id INT DEFAULT NULL, affinity_entity_id INT DEFAULT NULL, leader_entity_id INT DEFAULT NULL, rarity_entity_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_578C8FC7989D9B62 (slug), INDEX IDX_578C8FC7D4A634AE (faction_entity_id), INDEX IDX_578C8FC735E33C4D (type_entity_id), INDEX IDX_578C8FC7F6C57F2E (allegiance_entity_id), INDEX IDX_578C8FC7A6FF8FC1 (affinity_entity_id), INDEX IDX_578C8FC7E57574F9 (leader_entity_id), INDEX IDX_578C8FC740CF86B1 (rarity_entity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_buffs (heroes_id INT NOT NULL, buffs_id INT NOT NULL, INDEX IDX_B4DF0CCEAAB40E2D (heroes_id), INDEX IDX_B4DF0CCE61E9FAD9 (buffs_id), PRIMARY KEY (heroes_id, buffs_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_debuffs (heroes_id INT NOT NULL, debuffs_id INT NOT NULL, INDEX IDX_2A7AF4E7AAB40E2D (heroes_id), INDEX IDX_2A7AF4E729B92D27 (debuffs_id), PRIMARY KEY (heroes_id, debuffs_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_disable (heroes_id INT NOT NULL, disable_id INT NOT NULL, INDEX IDX_D671BF61AAB40E2D (heroes_id), INDEX IDX_D671BF61485E05AF (disable_id), PRIMARY KEY (heroes_id, disable_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_instants (heroes_id INT NOT NULL, instants_id INT NOT NULL, INDEX IDX_95278447AAB40E2D (heroes_id), INDEX IDX_952784479DE331CC (instants_id), PRIMARY KEY (heroes_id, instants_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_sets (heroes_id INT NOT NULL, sets_id INT NOT NULL, INDEX IDX_3A728E1BAAB40E2D (heroes_id), INDEX IDX_3A728E1BF40DDE7E (sets_id), PRIMARY KEY (heroes_id, sets_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_armor (heroes_id INT NOT NULL, armor_id INT NOT NULL, INDEX IDX_C4382A99AAB40E2D (heroes_id), INDEX IDX_C4382A99F5AA3663 (armor_id), PRIMARY KEY (heroes_id, armor_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_weapons (heroes_id INT NOT NULL, weapons_id INT NOT NULL, INDEX IDX_CB3A49D1AAB40E2D (heroes_id), INDEX IDX_CB3A49D12EE82581 (weapons_id), PRIMARY KEY (heroes_id, weapons_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE heroes_imprints (heroes_id INT NOT NULL, imprints_id INT NOT NULL, INDEX IDX_68AF313BAAB40E2D (heroes_id), INDEX IDX_68AF313BBAD9F1EA (imprints_id), PRIMARY KEY (heroes_id, imprints_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE imprints (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, rarity_id INT DEFAULT NULL, INDEX IDX_4EEF3565F3747573 (rarity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE instants (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, category VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE leader (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rarity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, effect LONGTEXT DEFAULT NULL, piece_type INT DEFAULT NULL, base_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE skill_upgrade (id INT AUTO_INCREMENT NOT NULL, skill_type VARCHAR(20) NOT NULL, cooldown INT DEFAULT NULL, level1 LONGTEXT DEFAULT NULL, level2 LONGTEXT DEFAULT NULL, level3 LONGTEXT DEFAULT NULL, level4 LONGTEXT DEFAULT NULL, level5 LONGTEXT DEFAULT NULL, level6 LONGTEXT DEFAULT NULL, hero_id INT NOT NULL, INDEX IDX_AD93EA3345B0BCD (hero_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE takeovers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, details VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, is_verified TINYINT NOT NULL, created_at DATETIME NOT NULL, last_login_at DATETIME DEFAULT NULL, is_active TINYINT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE weapons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, rarity VARCHAR(50) DEFAULT NULL, main_stat VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, faction VARCHAR(100) DEFAULT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE awakening ADD CONSTRAINT FK_A1F39B7845B0BCD FOREIGN KEY (hero_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_passive ADD CONSTRAINT FK_2BC9514CB606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_passive ADD CONSTRAINT FK_2BC9514C99091188 FOREIGN KEY (phase_id) REFERENCES dungeon_phase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_phase ADD CONSTRAINT FK_2BF7829B606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7D4A634AE FOREIGN KEY (faction_entity_id) REFERENCES faction (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC735E33C4D FOREIGN KEY (type_entity_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7F6C57F2E FOREIGN KEY (allegiance_entity_id) REFERENCES allegiance (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7A6FF8FC1 FOREIGN KEY (affinity_entity_id) REFERENCES affinity (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7E57574F9 FOREIGN KEY (leader_entity_id) REFERENCES leader (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC740CF86B1 FOREIGN KEY (rarity_entity_id) REFERENCES rarity (id)');
        $this->addSql('ALTER TABLE heroes_buffs ADD CONSTRAINT FK_B4DF0CCEAAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_buffs ADD CONSTRAINT FK_B4DF0CCE61E9FAD9 FOREIGN KEY (buffs_id) REFERENCES buffs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_debuffs ADD CONSTRAINT FK_2A7AF4E7AAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_debuffs ADD CONSTRAINT FK_2A7AF4E729B92D27 FOREIGN KEY (debuffs_id) REFERENCES debuffs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_disable ADD CONSTRAINT FK_D671BF61AAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_disable ADD CONSTRAINT FK_D671BF61485E05AF FOREIGN KEY (disable_id) REFERENCES disable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_instants ADD CONSTRAINT FK_95278447AAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_instants ADD CONSTRAINT FK_952784479DE331CC FOREIGN KEY (instants_id) REFERENCES instants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_sets ADD CONSTRAINT FK_3A728E1BAAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_sets ADD CONSTRAINT FK_3A728E1BF40DDE7E FOREIGN KEY (sets_id) REFERENCES sets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_armor ADD CONSTRAINT FK_C4382A99AAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_armor ADD CONSTRAINT FK_C4382A99F5AA3663 FOREIGN KEY (armor_id) REFERENCES armor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_weapons ADD CONSTRAINT FK_CB3A49D1AAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_weapons ADD CONSTRAINT FK_CB3A49D12EE82581 FOREIGN KEY (weapons_id) REFERENCES weapons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_imprints ADD CONSTRAINT FK_68AF313BAAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_imprints ADD CONSTRAINT FK_68AF313BBAD9F1EA FOREIGN KEY (imprints_id) REFERENCES imprints (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE imprints ADD CONSTRAINT FK_4EEF3565F3747573 FOREIGN KEY (rarity_id) REFERENCES rarity (id)');
        $this->addSql('ALTER TABLE skill_upgrade ADD CONSTRAINT FK_AD93EA3345B0BCD FOREIGN KEY (hero_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dungeon_team_suggestion ADD CONSTRAINT FK_AF9057ACB606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id)');
        $this->addSql('ALTER TABLE team_suggestion_heroes ADD CONSTRAINT FK_7166944DB3D6394C FOREIGN KEY (dungeon_team_suggestion_id) REFERENCES dungeon_team_suggestion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_suggestion_heroes ADD CONSTRAINT FK_7166944DAAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hero_tier_list CHANGE category category VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE hero_tier_list ADD CONSTRAINT FK_1CE1B81A45B0BCD FOREIGN KEY (hero_id) REFERENCES heroes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE awakening DROP FOREIGN KEY FK_A1F39B7845B0BCD');
        $this->addSql('ALTER TABLE dungeon_passive DROP FOREIGN KEY FK_2BC9514CB606863');
        $this->addSql('ALTER TABLE dungeon_passive DROP FOREIGN KEY FK_2BC9514C99091188');
        $this->addSql('ALTER TABLE dungeon_phase DROP FOREIGN KEY FK_2BF7829B606863');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7D4A634AE');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC735E33C4D');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7F6C57F2E');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7A6FF8FC1');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7E57574F9');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC740CF86B1');
        $this->addSql('ALTER TABLE heroes_buffs DROP FOREIGN KEY FK_B4DF0CCEAAB40E2D');
        $this->addSql('ALTER TABLE heroes_buffs DROP FOREIGN KEY FK_B4DF0CCE61E9FAD9');
        $this->addSql('ALTER TABLE heroes_debuffs DROP FOREIGN KEY FK_2A7AF4E7AAB40E2D');
        $this->addSql('ALTER TABLE heroes_debuffs DROP FOREIGN KEY FK_2A7AF4E729B92D27');
        $this->addSql('ALTER TABLE heroes_disable DROP FOREIGN KEY FK_D671BF61AAB40E2D');
        $this->addSql('ALTER TABLE heroes_disable DROP FOREIGN KEY FK_D671BF61485E05AF');
        $this->addSql('ALTER TABLE heroes_instants DROP FOREIGN KEY FK_95278447AAB40E2D');
        $this->addSql('ALTER TABLE heroes_instants DROP FOREIGN KEY FK_952784479DE331CC');
        $this->addSql('ALTER TABLE heroes_sets DROP FOREIGN KEY FK_3A728E1BAAB40E2D');
        $this->addSql('ALTER TABLE heroes_sets DROP FOREIGN KEY FK_3A728E1BF40DDE7E');
        $this->addSql('ALTER TABLE heroes_armor DROP FOREIGN KEY FK_C4382A99AAB40E2D');
        $this->addSql('ALTER TABLE heroes_armor DROP FOREIGN KEY FK_C4382A99F5AA3663');
        $this->addSql('ALTER TABLE heroes_weapons DROP FOREIGN KEY FK_CB3A49D1AAB40E2D');
        $this->addSql('ALTER TABLE heroes_weapons DROP FOREIGN KEY FK_CB3A49D12EE82581');
        $this->addSql('ALTER TABLE heroes_imprints DROP FOREIGN KEY FK_68AF313BAAB40E2D');
        $this->addSql('ALTER TABLE heroes_imprints DROP FOREIGN KEY FK_68AF313BBAD9F1EA');
        $this->addSql('ALTER TABLE imprints DROP FOREIGN KEY FK_4EEF3565F3747573');
        $this->addSql('ALTER TABLE skill_upgrade DROP FOREIGN KEY FK_AD93EA3345B0BCD');
        $this->addSql('DROP TABLE affinity');
        $this->addSql('DROP TABLE allegiance');
        $this->addSql('DROP TABLE armor');
        $this->addSql('DROP TABLE awakening');
        $this->addSql('DROP TABLE buffs');
        $this->addSql('DROP TABLE debuffs');
        $this->addSql('DROP TABLE disable');
        $this->addSql('DROP TABLE dungeon_passive');
        $this->addSql('DROP TABLE dungeon_phase');
        $this->addSql('DROP TABLE dungeons');
        $this->addSql('DROP TABLE faction');
        $this->addSql('DROP TABLE heroes');
        $this->addSql('DROP TABLE heroes_buffs');
        $this->addSql('DROP TABLE heroes_debuffs');
        $this->addSql('DROP TABLE heroes_disable');
        $this->addSql('DROP TABLE heroes_instants');
        $this->addSql('DROP TABLE heroes_sets');
        $this->addSql('DROP TABLE heroes_armor');
        $this->addSql('DROP TABLE heroes_weapons');
        $this->addSql('DROP TABLE heroes_imprints');
        $this->addSql('DROP TABLE imprints');
        $this->addSql('DROP TABLE instants');
        $this->addSql('DROP TABLE leader');
        $this->addSql('DROP TABLE rarity');
        $this->addSql('DROP TABLE sets');
        $this->addSql('DROP TABLE skill_upgrade');
        $this->addSql('DROP TABLE takeovers');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE weapons');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE dungeon_team_suggestion DROP FOREIGN KEY FK_AF9057ACB606863');
        $this->addSql('ALTER TABLE hero_tier_list DROP FOREIGN KEY FK_1CE1B81A45B0BCD');
        $this->addSql('ALTER TABLE hero_tier_list CHANGE category category VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE team_suggestion_heroes DROP FOREIGN KEY FK_7166944DB3D6394C');
        $this->addSql('ALTER TABLE team_suggestion_heroes DROP FOREIGN KEY FK_7166944DAAB40E2D');
    }
}
