<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117101829 extends AbstractMigration
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
        $this->addSql('CREATE TABLE faction (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE leader (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE heroes ADD faction_entity_id INT DEFAULT NULL, ADD type_entity_id INT DEFAULT NULL, ADD allegiance_entity_id INT DEFAULT NULL, ADD affinity_entity_id INT DEFAULT NULL, ADD leader_entity_id INT DEFAULT NULL, DROP faction, DROP type, DROP allegiance, DROP affinity, DROP leader, DROP faction_url, DROP type_icon_url, DROP affinity_icon_url, DROP allegiance_icon_url');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7D4A634AE FOREIGN KEY (faction_entity_id) REFERENCES faction (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC735E33C4D FOREIGN KEY (type_entity_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7F6C57F2E FOREIGN KEY (allegiance_entity_id) REFERENCES allegiance (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7A6FF8FC1 FOREIGN KEY (affinity_entity_id) REFERENCES affinity (id)');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC7E57574F9 FOREIGN KEY (leader_entity_id) REFERENCES leader (id)');
        $this->addSql('CREATE INDEX IDX_578C8FC7D4A634AE ON heroes (faction_entity_id)');
        $this->addSql('CREATE INDEX IDX_578C8FC735E33C4D ON heroes (type_entity_id)');
        $this->addSql('CREATE INDEX IDX_578C8FC7F6C57F2E ON heroes (allegiance_entity_id)');
        $this->addSql('CREATE INDEX IDX_578C8FC7A6FF8FC1 ON heroes (affinity_entity_id)');
        $this->addSql('CREATE INDEX IDX_578C8FC7E57574F9 ON heroes (leader_entity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE affinity');
        $this->addSql('DROP TABLE allegiance');
        $this->addSql('DROP TABLE faction');
        $this->addSql('DROP TABLE leader');
        $this->addSql('DROP TABLE type');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7D4A634AE');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC735E33C4D');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7F6C57F2E');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7A6FF8FC1');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC7E57574F9');
        $this->addSql('DROP INDEX IDX_578C8FC7D4A634AE ON heroes');
        $this->addSql('DROP INDEX IDX_578C8FC735E33C4D ON heroes');
        $this->addSql('DROP INDEX IDX_578C8FC7F6C57F2E ON heroes');
        $this->addSql('DROP INDEX IDX_578C8FC7A6FF8FC1 ON heroes');
        $this->addSql('DROP INDEX IDX_578C8FC7E57574F9 ON heroes');
        $this->addSql('ALTER TABLE heroes ADD faction VARCHAR(255) DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL, ADD allegiance VARCHAR(255) DEFAULT NULL, ADD affinity VARCHAR(255) DEFAULT NULL, ADD leader VARCHAR(255) DEFAULT NULL, ADD faction_url VARCHAR(500) DEFAULT NULL, ADD type_icon_url VARCHAR(500) DEFAULT NULL, ADD affinity_icon_url VARCHAR(500) DEFAULT NULL, ADD allegiance_icon_url VARCHAR(500) DEFAULT NULL, DROP faction_entity_id, DROP type_entity_id, DROP allegiance_entity_id, DROP affinity_entity_id, DROP leader_entity_id');
    }
}
