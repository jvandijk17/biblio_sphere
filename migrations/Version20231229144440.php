<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229144440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_unique_loan_book ON loan');
        $this->addSql('ALTER TABLE loan ADD status VARCHAR(50) NOT NULL, DROP book_if_not_returned');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan ADD book_if_not_returned INT DEFAULT NULL, DROP status');
        $this->addSql('CREATE UNIQUE INDEX idx_unique_loan_book ON loan (book_if_not_returned)');
    }
}
