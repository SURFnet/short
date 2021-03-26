<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210326051802 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE short_urls ADD institution_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE short_urls ADD CONSTRAINT FK_4A53F93410405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
        $this->addSql('CREATE INDEX IDX_4A53F93410405986 ON short_urls (institution_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE short_urls DROP FOREIGN KEY FK_4A53F93410405986');
        $this->addSql('DROP INDEX IDX_4A53F93410405986 ON short_urls');
        $this->addSql('ALTER TABLE short_urls DROP institution_id');
    }
}
