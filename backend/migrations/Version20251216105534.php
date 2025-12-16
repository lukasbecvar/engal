<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20251216095550
 *
 * The database migration for add database indexes
 *
 * @package DoctrineMigrations
 */
final class Version20251216095550 extends AbstractMigration
{
    /**
     * Get description text
     * 
     * @return string The description text
     */
    public function getDescription(): string
    {
        return 'Add database indexes';
    }

    /**
     * Executes the migration up
     * 
     * @param Schema $schema The database schema
     * 
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX logs_name_idx ON logs (name)');
        $this->addSql('CREATE INDEX logs_time_idx ON logs (time)');
        $this->addSql('CREATE INDEX logs_status_idx ON logs (status)');
        $this->addSql('CREATE INDEX media_token_idx ON media (token)');
        $this->addSql('CREATE INDEX media_owner_id_idx ON media (owner_id)');
        $this->addSql('CREATE INDEX media_gallery_name_idx ON media (gallery_name)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    /**
     * Executes the migration down
     * 
     * @param Schema $schema The database schema
     * 
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX logs_name_idx ON logs');
        $this->addSql('DROP INDEX logs_time_idx ON logs');
        $this->addSql('DROP INDEX logs_status_idx ON logs');
        $this->addSql('DROP INDEX media_token_idx ON media');
        $this->addSql('DROP INDEX media_owner_id_idx ON media');
        $this->addSql('DROP INDEX media_gallery_name_idx ON media');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
