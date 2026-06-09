<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260609130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add spell images + rewards to dungeons, spell image overrides to dungeon_phase';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE dungeons ADD spell1_image_url VARCHAR(255) DEFAULT NULL, ADD spell2_image_url VARCHAR(255) DEFAULT NULL, ADD spell3_image_url VARCHAR(255) DEFAULT NULL, ADD rewards_json LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE dungeon_phase ADD spell1_image_override VARCHAR(255) DEFAULT NULL, ADD spell2_image_override VARCHAR(255) DEFAULT NULL, ADD spell3_image_override VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dungeon_passive ADD image_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE dungeons DROP COLUMN spell1_image_url, DROP COLUMN spell2_image_url, DROP COLUMN spell3_image_url, DROP COLUMN rewards_json');
        $this->addSql('ALTER TABLE dungeon_phase DROP COLUMN spell1_image_override, DROP COLUMN spell2_image_override, DROP COLUMN spell3_image_override');
        $this->addSql('ALTER TABLE dungeon_passive DROP COLUMN image_url');
    }
}
