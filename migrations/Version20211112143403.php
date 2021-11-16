<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112143403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_user_answered ADD answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_user_answered ADD CONSTRAINT FK_A742BCF9AA334807 FOREIGN KEY (answer_id) REFERENCES quiz_answer (id)');
        $this->addSql('CREATE INDEX IDX_A742BCF9AA334807 ON quiz_user_answered (answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_user_answered DROP FOREIGN KEY FK_A742BCF9AA334807');
        $this->addSql('DROP INDEX IDX_A742BCF9AA334807 ON quiz_user_answered');
        $this->addSql('ALTER TABLE quiz_user_answered DROP answer_id');
    }
}
