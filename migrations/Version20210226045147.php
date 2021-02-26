<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210226045147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE institution (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, contact_email VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, logo_url VARCHAR(255) NOT NULL, alternate_logo_url VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sf_user ADD institution_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sf_user ADD CONSTRAINT FK_88D5A8C010405986 FOREIGN KEY (institution_id) REFERENCES institution (id)');
        $this->addSql('CREATE INDEX IDX_88D5A8C010405986 ON sf_user (institution_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sf_user DROP FOREIGN KEY FK_88D5A8C010405986');
        $this->addSql('DROP TABLE institution');
        $this->addSql('DROP INDEX IDX_88D5A8C010405986 ON sf_user');
        $this->addSql('ALTER TABLE sf_user DROP institution_id');
    }
}
