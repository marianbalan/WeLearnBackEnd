<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220320203618 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE study_group_user (study_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(study_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_2355E9595DDDCCCE ON study_group_user (study_group_id)');
        $this->addSql('CREATE INDEX IDX_2355E959A76ED395 ON study_group_user (user_id)');
        $this->addSql('ALTER TABLE study_group_user ADD CONSTRAINT FK_2355E9595DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE study_group_user ADD CONSTRAINT FK_2355E959A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE study_group_user');
    }
}
