<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219180947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DDAA1CDA8F93B6FC ON episode (movie_id)');
        $this->addSql('ALTER TABLE review ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE review ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE review ALTER created_at SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE review ALTER created_at SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE review ALTER created_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN review.created_at IS NULL');
        $this->addSql('ALTER TABLE episode DROP CONSTRAINT FK_DDAA1CDA8F93B6FC');
        $this->addSql('DROP INDEX IDX_DDAA1CDA8F93B6FC');
    }
}
