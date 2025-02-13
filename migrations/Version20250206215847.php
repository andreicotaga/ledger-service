<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206215847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add the `id` column as nullable
        $this->addSql('ALTER TABLE ledger_balances ADD id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN ledger_balances.id IS \'(DC2Type:uuid)\'');

        // Step 2: Generate UUIDs for existing rows
        $this->addSql('UPDATE ledger_balances SET id = gen_random_uuid()');

        // Step 3: Set `id` as NOT NULL
        $this->addSql('ALTER TABLE ledger_balances ALTER COLUMN id SET NOT NULL');

        // Step 4: Drop the old primary key
        $this->addSql('ALTER TABLE ledger_balances DROP CONSTRAINT ledger_balances_pkey');

        // Step 5: Set `id` as the new primary key
        $this->addSql('ALTER TABLE ledger_balances ADD PRIMARY KEY (id)');

        // Step 6: Add index for `ledger_id`
        $this->addSql('CREATE INDEX IDX_10B7E1F5A7B913DD ON ledger_balances (ledger_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_10B7E1F5A7B913DD');
        $this->addSql('DROP INDEX ledger_balances_pkey');
        $this->addSql('ALTER TABLE ledger_balances DROP id');
        $this->addSql('ALTER TABLE ledger_balances ADD PRIMARY KEY (ledger_id)');
    }
}
