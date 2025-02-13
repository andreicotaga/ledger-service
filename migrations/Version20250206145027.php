<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206145027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ledger (id UUID NOT NULL, name VARCHAR(255) NOT NULL, base_currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN ledger.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE ledger_balances (ledger_id UUID NOT NULL, currency VARCHAR(3) NOT NULL, balance NUMERIC(18, 6) NOT NULL, PRIMARY KEY(ledger_id))');
        $this->addSql('COMMENT ON COLUMN ledger_balances.ledger_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE transaction (id UUID NOT NULL, ledger_id UUID NOT NULL, transaction_type VARCHAR(10) NOT NULL, amount NUMERIC(18, 6) NOT NULL, currency VARCHAR(3) NOT NULL, transaction_reference VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D1ED84D250 ON transaction (transaction_reference)');
        $this->addSql('CREATE INDEX IDX_723705D1A7B913DD ON transaction (ledger_id)');
        $this->addSql('COMMENT ON COLUMN transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.ledger_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE ledger_balances ADD CONSTRAINT FK_10B7E1F5A7B913DD FOREIGN KEY (ledger_id) REFERENCES ledger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A7B913DD FOREIGN KEY (ledger_id) REFERENCES ledger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ledger_balances DROP CONSTRAINT FK_10B7E1F5A7B913DD');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1A7B913DD');
        $this->addSql('DROP TABLE ledger');
        $this->addSql('DROP TABLE ledger_balances');
        $this->addSql('DROP TABLE transaction');
    }
}
