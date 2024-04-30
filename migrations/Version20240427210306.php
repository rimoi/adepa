<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240427210306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD categories JSON DEFAULT NULL, ADD end_at DATETIME DEFAULT NULL, ADD number_intervention INT DEFAULT NULL, ADD price DOUBLE PRECISION DEFAULT NULL, DROP time_slot, CHANGE date_slot started_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD reservation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2B83297E7 ON service (reservation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2B83297E7');
        $this->addSql('DROP INDEX IDX_E19D9AD2B83297E7 ON service');
        $this->addSql('ALTER TABLE service DROP reservation_id');
        $this->addSql('ALTER TABLE reservation ADD date_slot DATETIME DEFAULT NULL, ADD time_slot VARCHAR(255) DEFAULT NULL, DROP categories, DROP started_at, DROP end_at, DROP number_intervention, DROP price');
    }
}
