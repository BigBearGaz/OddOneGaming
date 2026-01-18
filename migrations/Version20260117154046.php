<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117154046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rarity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE heroes ADD rarity_entity_id INT DEFAULT NULL, CHANGE imprint imprint LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE heroes ADD CONSTRAINT FK_578C8FC740CF86B1 FOREIGN KEY (rarity_entity_id) REFERENCES rarity (id)');
        $this->addSql('CREATE INDEX IDX_578C8FC740CF86B1 ON heroes (rarity_entity_id)');
        $this->addSql('ALTER TABLE imprints ADD rarity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE imprints ADD CONSTRAINT FK_4EEF3565F3747573 FOREIGN KEY (rarity_id) REFERENCES rarity (id)');
        $this->addSql('CREATE INDEX IDX_4EEF3565F3747573 ON imprints (rarity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rarity');
        $this->addSql('ALTER TABLE heroes DROP FOREIGN KEY FK_578C8FC740CF86B1');
        $this->addSql('DROP INDEX IDX_578C8FC740CF86B1 ON heroes');
        $this->addSql('ALTER TABLE heroes DROP rarity_entity_id, CHANGE imprint imprint VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE imprints DROP FOREIGN KEY FK_4EEF3565F3747573');
        $this->addSql('DROP INDEX IDX_4EEF3565F3747573 ON imprints');
        $this->addSql('ALTER TABLE imprints DROP rarity_id');
    }
}
