<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009103645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE loan ADD COLUMN book_if_not_returned INT AS (CASE WHEN return_date IS NULL THEN book_id ELSE NULL END) VIRTUAL');
        
    $this->addSql('ALTER TABLE loan ADD UNIQUE INDEX idx_unique_loan_book (book_if_not_returned)');
}

public function down(Schema $schema): void
{
    $this->addSql('ALTER TABLE loan DROP INDEX idx_unique_loan_book');
    $this->addSql('ALTER TABLE loan DROP COLUMN book_if_not_returned');
}

}
