<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260721123107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tache (id SERIAL NOT NULL, titre VARCHAR(150) NOT NULL, description TEXT DEFAULT NULL, data_creation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, data_echeance TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, priorite VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, assigne_a_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_93872075BB1B0F33 ON tache (assigne_a_id)');

        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');

        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        $this->addSql("COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql("COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql("COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'");

        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075BB1B0F33 FOREIGN KEY (assigne_a_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tache DROP CONSTRAINT FK_93872075BB1B0F33');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}