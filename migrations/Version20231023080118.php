<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023080118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendance (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, level_id INT NOT NULL, career_id INT NOT NULL, subject_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_6DE30D9154177093 (room_id), INDEX IDX_6DE30D915FB14BA7 (level_id), INDEX IDX_6DE30D91B58CDA09 (career_id), INDEX IDX_6DE30D9123EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9154177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D915FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91B58CDA09 FOREIGN KEY (career_id) REFERENCES career (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9123EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D9154177093');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D915FB14BA7');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91B58CDA09');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D9123EDC87');
        $this->addSql('DROP TABLE attendance');
    }
}
