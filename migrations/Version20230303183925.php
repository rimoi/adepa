<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230303183925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, file_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published TINYINT(1) NOT NULL, archived TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_23A0E66A76ED395 (user_id), UNIQUE INDEX UNIQ_23A0E6693CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, mission_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', archived TINYINT(1) NOT NULL, INDEX IDX_E00CEDDEA76ED395 (user_id), INDEX IDX_E00CEDDEBE6CAE90 (mission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT DEFAULT NULL, archived TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, type VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, sender VARCHAR(255) NOT NULL, receiver VARCHAR(255) NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', send TINYINT(1) DEFAULT 0 NOT NULL, template LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', year VARCHAR(255) NOT NULL, INDEX IDX_590C103A76ED395 (user_id), UNIQUE INDEX UNIQ_590C10393CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mission (id INT AUTO_INCREMENT NOT NULL, file_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', started DATETIME NOT NULL, ended DATETIME DEFAULT NULL, booked TINYINT(1) DEFAULT 0 NOT NULL, archived TINYINT(1) NOT NULL, published TINYINT(1) DEFAULT 0 NOT NULL, slug VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, zip_code INT NOT NULL, city VARCHAR(255) NOT NULL, category VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, emergency TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_9067F23C93CB796C (file_id), INDEX IDX_9067F23CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mission_category (mission_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_EB01878BE6CAE90 (mission_id), INDEX IDX_EB0187812469DE2 (category_id), PRIMARY KEY(mission_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qualification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, entreprise VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', year VARCHAR(255) NOT NULL, INDEX IDX_B712F0CEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, iban_id INT DEFAULT NULL, cni_id INT DEFAULT NULL, permis_conduite_id INT DEFAULT NULL, autoentreprise_certificate_id INT DEFAULT NULL, file_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, adress VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, tva VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, archived TINYINT(1) DEFAULT 0 NOT NULL, slug VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64920D5BAF6 (iban_id), UNIQUE INDEX UNIQ_8D93D649C68EB1D0 (cni_id), UNIQUE INDEX UNIQ_8D93D649854F43FD (permis_conduite_id), UNIQUE INDEX UNIQ_8D93D64990D9BC9E (autoentreprise_certificate_id), UNIQUE INDEX UNIQ_8D93D64993CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_category (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_E6C1FDC1A76ED395 (user_id), INDEX IDX_E6C1FDC112469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6693CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE experience ADD CONSTRAINT FK_590C103A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE experience ADD CONSTRAINT FK_590C10393CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23C93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mission_category ADD CONSTRAINT FK_EB01878BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission_category ADD CONSTRAINT FK_EB0187812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qualification ADD CONSTRAINT FK_B712F0CEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64920D5BAF6 FOREIGN KEY (iban_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C68EB1D0 FOREIGN KEY (cni_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649854F43FD FOREIGN KEY (permis_conduite_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64990D9BC9E FOREIGN KEY (autoentreprise_certificate_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64993CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6693CB796C');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA76ED395');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEBE6CAE90');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE experience DROP FOREIGN KEY FK_590C103A76ED395');
        $this->addSql('ALTER TABLE experience DROP FOREIGN KEY FK_590C10393CB796C');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C93CB796C');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CA76ED395');
        $this->addSql('ALTER TABLE mission_category DROP FOREIGN KEY FK_EB01878BE6CAE90');
        $this->addSql('ALTER TABLE mission_category DROP FOREIGN KEY FK_EB0187812469DE2');
        $this->addSql('ALTER TABLE qualification DROP FOREIGN KEY FK_B712F0CEA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64920D5BAF6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C68EB1D0');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649854F43FD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64990D9BC9E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64993CB796C');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC1A76ED395');
        $this->addSql('ALTER TABLE user_category DROP FOREIGN KEY FK_E6C1FDC112469DE2');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE mission');
        $this->addSql('DROP TABLE mission_category');
        $this->addSql('DROP TABLE qualification');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_category');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
