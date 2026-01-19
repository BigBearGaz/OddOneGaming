<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119060432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dungeon_team_suggestion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, difficulty VARCHAR(50) DEFAULT NULL, dungeon_id INT NOT NULL, INDEX IDX_AF9057ACB606863 (dungeon_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_suggestion_heroes (dungeon_team_suggestion_id INT NOT NULL, heroes_id INT NOT NULL, INDEX IDX_7166944DB3D6394C (dungeon_team_suggestion_id), INDEX IDX_7166944DAAB40E2D (heroes_id), PRIMARY KEY (dungeon_team_suggestion_id, heroes_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hero_tier_list (id INT AUTO_INCREMENT NOT NULL, tier VARCHAR(10) NOT NULL, category VARCHAR(50) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, ranking_order INT NOT NULL, updated_at DATETIME NOT NULL, hero_id INT NOT NULL, INDEX IDX_1CE1B81A45B0BCD (hero_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE dungeon_team_suggestion ADD CONSTRAINT FK_AF9057ACB606863 FOREIGN KEY (dungeon_id) REFERENCES dungeons (id)');
        $this->addSql('ALTER TABLE team_suggestion_heroes ADD CONSTRAINT FK_7166944DB3D6394C FOREIGN KEY (dungeon_team_suggestion_id) REFERENCES dungeon_team_suggestion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_suggestion_heroes ADD CONSTRAINT FK_7166944DAAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hero_tier_list ADD CONSTRAINT FK_1CE1B81A45B0BCD FOREIGN KEY (hero_id) REFERENCES heroes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dungeon_team_suggestion DROP FOREIGN KEY FK_AF9057ACB606863');
        $this->addSql('ALTER TABLE team_suggestion_heroes DROP FOREIGN KEY FK_7166944DB3D6394C');
        $this->addSql('ALTER TABLE team_suggestion_heroes DROP FOREIGN KEY FK_7166944DAAB40E2D');
        $this->addSql('ALTER TABLE hero_tier_list DROP FOREIGN KEY FK_1CE1B81A45B0BCD');
        $this->addSql('DROP TABLE dungeon_team_suggestion');
        $this->addSql('DROP TABLE team_suggestion_heroes');
        $this->addSql('DROP TABLE hero_tier_list');
    }
}
