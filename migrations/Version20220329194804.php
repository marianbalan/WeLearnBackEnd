<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220329194804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_group ADD class_master_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE study_group ADD CONSTRAINT FK_32BA1425786C442E FOREIGN KEY (class_master_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32BA1425786C442E ON study_group (class_master_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE study_group DROP CONSTRAINT FK_32BA1425786C442E');
        $this->addSql('DROP INDEX UNIQ_32BA1425786C442E');
        $this->addSql('ALTER TABLE study_group DROP class_master_id');
    }
}
