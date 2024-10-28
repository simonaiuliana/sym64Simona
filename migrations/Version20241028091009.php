<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241028091621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create article table';
    }

    public function up(Schema $schema): void
    {
        // Check if the article table exists before creating it
        if (!$schema->hasTable('article')) {
            $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema): void
    {
        // Drop the article table if it exists
        if ($schema->hasTable('article')) {
            $this->addSql('DROP TABLE article');
        }
    }
}
