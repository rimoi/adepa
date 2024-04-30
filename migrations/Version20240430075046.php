<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240430075046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE new_request (id INT AUTO_INCREMENT NOT NULL, educatheur_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_E0D4DC71683FB416 (educatheur_id), INDEX IDX_E0D4DC71A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE new_request ADD CONSTRAINT FK_E0D4DC71683FB416 FOREIGN KEY (educatheur_id) REFERENCES educatheure (id)');
        $this->addSql('ALTER TABLE new_request ADD CONSTRAINT FK_E0D4DC71A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE new_request DROP FOREIGN KEY FK_E0D4DC71683FB416');
        $this->addSql('ALTER TABLE new_request DROP FOREIGN KEY FK_E0D4DC71A76ED395');
        $this->addSql('DROP TABLE new_request');
    }
}
