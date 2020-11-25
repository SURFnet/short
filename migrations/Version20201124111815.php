<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201124111815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create user table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE sf_user (id VARCHAR(256) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO sf_user (id, roles, enabled) SELECT DISTINCT owner, \'["ROLE_USER"]\', 1 FROM short_urls;');
        $this->addSql('ALTER TABLE short_urls ADD CONSTRAINT FK_4A53F934CF60E67C FOREIGN KEY (owner) REFERENCES sf_user (id)');
        $this->addSql('CREATE INDEX IDX_4A53F934CF60E67C ON short_urls (owner)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE short_urls DROP FOREIGN KEY FK_4A53F934CF60E67C');
        $this->addSql('DROP TABLE sf_user');
        $this->addSql('DROP INDEX IDX_4A53F934CF60E67C ON short_urls');
    }
}
