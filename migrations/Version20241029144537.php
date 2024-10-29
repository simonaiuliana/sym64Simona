<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029144537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66D823E37A');
        $this->addSql('DROP INDEX IDX_23A0E66D823E37A ON article');
        $this->addSql('DROP INDEX UNIQ_23A0E66D347411D ON article');
        $this->addSql('ALTER TABLE article DROP section_id, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT UNSIGNED NOT NULL, CHANGE article_date_create article_date_create DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE published published TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE article_section ADD CONSTRAINT FK_C0A13E587294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_section ADD CONSTRAINT FK_C0A13E58D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE section CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE section_detail section_detail VARCHAR(500) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D737AEF1D237769 ON section (section_slug)');
        $this->addSql('ALTER TABLE user CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE activate activate TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD section_id INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE article_date_create article_date_create DATETIME NOT NULL, CHANGE published published SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_23A0E66D823E37A ON article (section_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66D347411D ON article (title_slug)');
        $this->addSql('ALTER TABLE article_section DROP FOREIGN KEY FK_C0A13E587294869C');
        $this->addSql('ALTER TABLE article_section DROP FOREIGN KEY FK_C0A13E58D823E37A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7294869C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('DROP INDEX UNIQ_2D737AEF1D237769 ON section');
        $this->addSql('ALTER TABLE section CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE section_detail section_detail LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE activate activate SMALLINT DEFAULT 0 NOT NULL');
    }
}
