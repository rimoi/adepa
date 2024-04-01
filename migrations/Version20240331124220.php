<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331124220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE educatheure_category (educatheure_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_D0EF141266BF3F83 (educatheure_id), INDEX IDX_D0EF141212469DE2 (category_id), PRIMARY KEY(educatheure_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE educatheure_category ADD CONSTRAINT FK_D0EF141266BF3F83 FOREIGN KEY (educatheure_id) REFERENCES educatheure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE educatheure_category ADD CONSTRAINT FK_D0EF141212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE educatheure_category DROP FOREIGN KEY FK_D0EF141266BF3F83');
        $this->addSql('ALTER TABLE educatheure_category DROP FOREIGN KEY FK_D0EF141212469DE2');
        $this->addSql('DROP TABLE educatheure_category');
    }
}
