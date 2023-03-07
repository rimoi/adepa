<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306163919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sms ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sms ADD CONSTRAINT FK_B0A93A77A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B0A93A77A76ED395 ON sms (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sms DROP FOREIGN KEY FK_B0A93A77A76ED395');
        $this->addSql('DROP INDEX IDX_B0A93A77A76ED395 ON sms');
        $this->addSql('ALTER TABLE sms DROP user_id');
    }
}
