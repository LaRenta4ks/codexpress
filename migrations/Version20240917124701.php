<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240917124701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE network ADD creator_id INT NOT NULL, DROP creator, CHANGE name name VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE network ADD CONSTRAINT FK_608487BC61220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_608487BC61220EA6 ON network (creator_id)');
        $this->addSql('ALTER TABLE note CHANGE title title VARCHAR(80) NOT NULL, CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP image');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE network DROP FOREIGN KEY FK_608487BC61220EA6');
        $this->addSql('DROP INDEX IDX_608487BC61220EA6 ON network');
        $this->addSql('ALTER TABLE network ADD creator BIGINT NOT NULL, DROP creator_id, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE note CHANGE title title VARCHAR(80) DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD image VARCHAR(255) DEFAULT NULL');
    }
}
