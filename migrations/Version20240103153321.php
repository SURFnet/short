<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240103153321 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add new field "label"';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE short_urls ADD COLUMN label VARCHAR(255) NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE short_urls DROP COLUMN label');
    }
}
