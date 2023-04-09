<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409132233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD criminal_record_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495C980E05 FOREIGN KEY (criminal_record_id) REFERENCES file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495C980E05 ON user (criminal_record_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495C980E05');
        $this->addSql('DROP INDEX UNIQ_8D93D6495C980E05 ON user');
        $this->addSql('ALTER TABLE user DROP criminal_record_id');
        $this->addSql('ALTER TABLE mission ADD tasks VARCHAR(255) DEFAULT NULL');
    }
}
