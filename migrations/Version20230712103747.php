<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712103747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3316D51A9F4');
        $this->addSql('DROP INDEX fk_cbe5a3316d51a9f4 ON book');
        $this->addSql('CREATE INDEX IDX_CBE5A3316D51A9F4 ON book (library_id_fk_id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316D51A9F4 FOREIGN KEY (library_id_fk_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE book_category ADD book_id_fk_id INT DEFAULT NULL, ADD category_id_fk_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book_category ADD CONSTRAINT FK_1FB30F98520D4EB9 FOREIGN KEY (book_id_fk_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_category ADD CONSTRAINT FK_1FB30F98FC88DC9B FOREIGN KEY (category_id_fk_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_1FB30F98520D4EB9 ON book_category (book_id_fk_id)');
        $this->addSql('CREATE INDEX IDX_1FB30F98FC88DC9B ON book_category (category_id_fk_id)');
        $this->addSql('ALTER TABLE loan ADD user_id_fk_id INT DEFAULT NULL, ADD book_id_fk_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D03AB7FBCE0 FOREIGN KEY (user_id_fk_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D03520D4EB9 FOREIGN KEY (book_id_fk_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_C5D30D03AB7FBCE0 ON loan (user_id_fk_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D03520D4EB9 ON loan (book_id_fk_id)');
        $this->addSql('ALTER TABLE user ADD library_id_fk_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496D51A9F4 FOREIGN KEY (library_id_fk_id) REFERENCES library (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496D51A9F4 ON user (library_id_fk_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3316D51A9F4');
        $this->addSql('DROP INDEX idx_cbe5a3316d51a9f4 ON book');
        $this->addSql('CREATE INDEX FK_CBE5A3316D51A9F4 ON book (library_id_fk_id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3316D51A9F4 FOREIGN KEY (library_id_fk_id) REFERENCES library (id)');
        $this->addSql('ALTER TABLE book_category DROP FOREIGN KEY FK_1FB30F98520D4EB9');
        $this->addSql('ALTER TABLE book_category DROP FOREIGN KEY FK_1FB30F98FC88DC9B');
        $this->addSql('DROP INDEX IDX_1FB30F98520D4EB9 ON book_category');
        $this->addSql('DROP INDEX IDX_1FB30F98FC88DC9B ON book_category');
        $this->addSql('ALTER TABLE book_category DROP book_id_fk_id, DROP category_id_fk_id');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D03AB7FBCE0');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D03520D4EB9');
        $this->addSql('DROP INDEX IDX_C5D30D03AB7FBCE0 ON loan');
        $this->addSql('DROP INDEX IDX_C5D30D03520D4EB9 ON loan');
        $this->addSql('ALTER TABLE loan DROP user_id_fk_id, DROP book_id_fk_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496D51A9F4');
        $this->addSql('DROP INDEX IDX_8D93D6496D51A9F4 ON user');
        $this->addSql('ALTER TABLE user DROP library_id_fk_id');
    }
}
