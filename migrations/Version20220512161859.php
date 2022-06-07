<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512161859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment DROP CONSTRAINT fk_30c544ba5dddccce');
        $this->addSql('DROP INDEX idx_30c544ba5dddccce');
        $this->addSql('ALTER TABLE assignment DROP study_group_id');
        $this->addSql('ALTER TABLE assignment ALTER subject_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE assignment ADD study_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE assignment ALTER subject_id DROP NOT NULL');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT fk_30c544ba5dddccce FOREIGN KEY (study_group_id) REFERENCES study_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_30c544ba5dddccce ON assignment (study_group_id)');
    }
}
