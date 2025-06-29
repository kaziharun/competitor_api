<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250629004305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create product_prices table with all required fields';
    }

    public function up(Schema $schema): void
    {
        // Create the table with MySQL-compatible syntax
        $this->addSql('
        CREATE TABLE product_prices (
            id INT AUTO_INCREMENT NOT NULL,
            product_id VARCHAR(255) NOT NULL,
            vendor_name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            fetched_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    ');

        // Add indexes
        $this->addSql('CREATE INDEX IDX_PRODUCT_ID ON product_prices (product_id)');
        $this->addSql('CREATE INDEX IDX_FETCHED_AT ON product_prices (fetched_at)');
    }

    public function down(Schema $chema): void
    {
        $this->addSql('DROP TABLE product_prices');
    }
}
