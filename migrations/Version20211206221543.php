<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211206221543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_result_cache DROP INDEX UNIQ_64AB990A76ED395, ADD INDEX IDX_64AB990A76ED395 (user_id)');
        $this->addSql('ALTER TABLE quiz_result_cache CHANGE user_id user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_result_cache DROP INDEX IDX_64AB990A76ED395, ADD UNIQUE INDEX UNIQ_64AB990A76ED395 (user_id)');
        $this->addSql('ALTER TABLE quiz_result_cache CHANGE user_id user_id INT NOT NULL');
    }
}
