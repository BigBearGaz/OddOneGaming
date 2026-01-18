<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117152814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE heroes_imprints (heroes_id INT NOT NULL, imprints_id INT NOT NULL, INDEX IDX_68AF313BAAB40E2D (heroes_id), INDEX IDX_68AF313BBAD9F1EA (imprints_id), PRIMARY KEY (heroes_id, imprints_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE imprints (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE heroes_imprints ADD CONSTRAINT FK_68AF313BAAB40E2D FOREIGN KEY (heroes_id) REFERENCES heroes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE heroes_imprints ADD CONSTRAINT FK_68AF313BBAD9F1EA FOREIGN KEY (imprints_id) REFERENCES imprints (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE heroes_imprints DROP FOREIGN KEY FK_68AF313BAAB40E2D');
        $this->addSql('ALTER TABLE heroes_imprints DROP FOREIGN KEY FK_68AF313BBAD9F1EA');
        $this->addSql('DROP TABLE heroes_imprints');
        $this->addSql('DROP TABLE imprints');
    }
}
