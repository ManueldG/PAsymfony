<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611161427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_photo (tag_id INT NOT NULL, photo_id INT NOT NULL, INDEX IDX_365B9D1EBAD26311 (tag_id), INDEX IDX_365B9D1E7E9E4C8C (photo_id), PRIMARY KEY(tag_id, photo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_photo ADD CONSTRAINT FK_365B9D1EBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_photo ADD CONSTRAINT FK_365B9D1E7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_photo DROP FOREIGN KEY FK_365B9D1EBAD26311');
        $this->addSql('ALTER TABLE tag_photo DROP FOREIGN KEY FK_365B9D1E7E9E4C8C');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_photo');
    }
}
