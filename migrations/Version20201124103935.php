<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201124103935 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create short_urls table';
    }

    public function up(Schema $schema) : void
    {
        $tableShortUrlExists = $this->connection->getSchemaManager()->tablesExist('short_urls');
        $this->skipIf($tableShortUrlExists, 'TABLE short_urls already exists');

        $this->addSql('CREATE TABLE short_urls (id INT AUTO_INCREMENT NOT NULL, short_url VARCHAR(32) NOT NULL, long_url TEXT NOT NULL, owner VARCHAR(256) NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, clicks INT NOT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_4A53F934CF60E67CB23DB7B8 (owner, created), UNIQUE INDEX short_url (short_url), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE short_urls');
    }
}
