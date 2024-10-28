<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028131534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE text text LONGTEXT NOT NULL, CHANGE published published SMALLINT DEFAULT 0 NOT NULL, CHANGE article_data_create article_date_create DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66D347411D ON article (title_slug)');
        $this->addSql('ALTER TABLE user CHANGE activate activate SMALLINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_23A0E66D347411D ON article');
        $this->addSql('ALTER TABLE article CHANGE text text VARCHAR(255) NOT NULL, CHANGE published published SMALLINT NOT NULL, CHANGE article_date_create article_data_create DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE activate activate SMALLINT NOT NULL');
    }
}
