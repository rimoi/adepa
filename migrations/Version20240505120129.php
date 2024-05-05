<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505120129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE educatheure ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE educatheure ADD CONSTRAINT FK_1C7B197D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1C7B197D7E3C61F9 ON educatheure (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE educatheure DROP FOREIGN KEY FK_1C7B197D7E3C61F9');
        $this->addSql('DROP INDEX IDX_1C7B197D7E3C61F9 ON educatheure');
        $this->addSql('ALTER TABLE educatheure DROP owner_id');
    }
}
