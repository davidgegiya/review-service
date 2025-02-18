<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217160353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE episode (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, release_date DATE NOT NULL, movie_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE movie (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE review ADD sentiment_score DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE review ADD episode_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE episode');
        $this->addSql('DROP TABLE movie');
        $this->addSql('ALTER TABLE review DROP sentiment_score');
        $this->addSql('ALTER TABLE review DROP episode_id');
    }
}
