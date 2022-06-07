<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426092312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE study_group_user');
        $this->addSql('ALTER TABLE "user" ADD study_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6495DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6495DDDCCCE ON "user" (study_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE study_group_user (study_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(study_group_id, user_id))');
        $this->addSql('CREATE INDEX idx_2355e959a76ed395 ON study_group_user (user_id)');
        $this->addSql('CREATE INDEX idx_2355e9595dddccce ON study_group_user (study_group_id)');
        $this->addSql('ALTER TABLE study_group_user ADD CONSTRAINT fk_2355e9595dddccce FOREIGN KEY (study_group_id) REFERENCES study_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE study_group_user ADD CONSTRAINT fk_2355e959a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6495DDDCCCE');
        $this->addSql('DROP INDEX IDX_8D93D6495DDDCCCE');
        $this->addSql('ALTER TABLE "user" DROP study_group_id');
    }
}
