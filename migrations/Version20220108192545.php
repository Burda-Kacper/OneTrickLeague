<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220108192545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz ADD quiz_saved_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921D159D99 FOREIGN KEY (quiz_saved_id) REFERENCES quiz_saved (id)');
        $this->addSql('CREATE INDEX IDX_A412FA921D159D99 ON quiz (quiz_saved_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA921D159D99');
        $this->addSql('DROP INDEX IDX_A412FA921D159D99 ON quiz');
        $this->addSql('ALTER TABLE quiz DROP quiz_saved_id');
    }
}
