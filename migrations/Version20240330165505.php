<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330165505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE educatheure (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, price INT NOT NULL, min_duration VARCHAR(255) DEFAULT NULL, max_duration VARCHAR(255) DEFAULT NULL, number_participant INT DEFAULT NULL, public_type VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, days JSON DEFAULT NULL, archived TINYINT(1) DEFAULT NULL, published TINYINT(1) DEFAULT NULL, slug VARCHAR(255) NOT NULL, zip_code INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, note_booking LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1C7B197D3DA5256D (image_id), INDEX IDX_1C7B197DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE educatheure ADD CONSTRAINT FK_1C7B197D3DA5256D FOREIGN KEY (image_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE educatheure ADD CONSTRAINT FK_1C7B197DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE educatheure DROP FOREIGN KEY FK_1C7B197D3DA5256D');
        $this->addSql('ALTER TABLE educatheure DROP FOREIGN KEY FK_1C7B197DA76ED395');
        $this->addSql('DROP TABLE educatheure');
    }
}
