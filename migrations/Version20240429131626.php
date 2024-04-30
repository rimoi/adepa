<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429131626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_category (reservation_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_9BF9FBD5B83297E7 (reservation_id), INDEX IDX_9BF9FBD512469DE2 (category_id), PRIMARY KEY(reservation_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation_category ADD CONSTRAINT FK_9BF9FBD5B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_category ADD CONSTRAINT FK_9BF9FBD512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_category DROP FOREIGN KEY FK_9BF9FBD5B83297E7');
        $this->addSql('ALTER TABLE reservation_category DROP FOREIGN KEY FK_9BF9FBD512469DE2');
        $this->addSql('DROP TABLE reservation_category');
    }
}
