<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005150146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $result = $this->connection->fetchAllAssociative('SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = "book" AND CONSTRAINT_NAME = "FK_CBE5A331FE2541D7"');

        if (!empty($result)) {
            $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331FE2541D7');
        }

        $this->addSql('ALTER TABLE book CHANGE library_id library_id INT NOT NULL');

        if (!empty($result)) {
            $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331FE2541D7 FOREIGN KEY (library_id) REFERENCES library(id)');
        }
    }

    public function down(Schema $schema): void
    {
        $result = $this->connection->fetchAllAssociative('SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = "book" AND CONSTRAINT_NAME = "FK_CBE5A331FE2541D7"');

        if (!empty($result)) {
            $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331FE2541D7');
        }

        $this->addSql('ALTER TABLE book CHANGE library_id library_id INT DEFAULT NULL');

        if (!empty($result)) {
            $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331FE2541D7 FOREIGN KEY (library_id) REFERENCES library(id)');
        }
    }
}
