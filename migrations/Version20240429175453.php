<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429175453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD affected_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C72D4736 FOREIGN KEY (affected_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C84955C72D4736 ON reservation (affected_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C72D4736');
        $this->addSql('DROP INDEX IDX_42C84955C72D4736 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP affected_id');
    }
}
