<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204204045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id SERIAL NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(255) NOT NULL, symbol VARCHAR(10) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE exchange_rate (id SERIAL NOT NULL, base_currency_id_id INT NOT NULL, target_currency_id_id INT NOT NULL, rate NUMERIC(10, 6) NOT NULL, date DATE NOT NULL, source VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E9521FAB9A932C2C ON exchange_rate (base_currency_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E9521FAB94DC9318 ON exchange_rate (target_currency_id_id)');
        $this->addSql('CREATE TABLE ledgers (id SERIAL NOT NULL, currency_id INT NOT NULL, uid UUID NOT NULL, amount NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D9194B0838248176 ON ledgers (currency_id)');
        $this->addSql('COMMENT ON COLUMN ledgers.uid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB9A932C2C FOREIGN KEY (base_currency_id_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB94DC9318 FOREIGN KEY (target_currency_id_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ledgers ADD CONSTRAINT FK_D9194B0838248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE exchange_rate DROP CONSTRAINT FK_E9521FAB9A932C2C');
        $this->addSql('ALTER TABLE exchange_rate DROP CONSTRAINT FK_E9521FAB94DC9318');
        $this->addSql('ALTER TABLE ledgers DROP CONSTRAINT FK_D9194B0838248176');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE exchange_rate');
        $this->addSql('DROP TABLE ledgers');
    }
}
