<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331125957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE educatheure_user (educatheure_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_423640D566BF3F83 (educatheure_id), INDEX IDX_423640D5A76ED395 (user_id), PRIMARY KEY(educatheure_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE educatheure_user ADD CONSTRAINT FK_423640D566BF3F83 FOREIGN KEY (educatheure_id) REFERENCES educatheure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE educatheure_user ADD CONSTRAINT FK_423640D5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE educatheure DROP FOREIGN KEY FK_1C7B197DA76ED395');
        $this->addSql('DROP INDEX IDX_1C7B197DA76ED395 ON educatheure');
        $this->addSql('ALTER TABLE educatheure DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE educatheure_user DROP FOREIGN KEY FK_423640D566BF3F83');
        $this->addSql('ALTER TABLE educatheure_user DROP FOREIGN KEY FK_423640D5A76ED395');
        $this->addSql('DROP TABLE educatheure_user');
        $this->addSql('ALTER TABLE educatheure ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE educatheure ADD CONSTRAINT FK_1C7B197DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1C7B197DA76ED395 ON educatheure (user_id)');
    }
}
