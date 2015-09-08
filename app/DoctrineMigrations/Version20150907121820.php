<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150907121820 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Customer ADD max_monthly_space BIGINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX name ON Customer (name, url)');
        $this->addSql('ALTER TABLE ftpuser ADD logcount INT DEFAULT NULL, ADD logindate DATETIME NOT NULL, ADD modifdate DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX name ON Customer');
        $this->addSql('ALTER TABLE Customer DROP max_monthly_space');
        $this->addSql('ALTER TABLE FtpUser DROP logcount, DROP logindate, DROP modifdate');
    }
}
