<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112163953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_user_answered ADD question_id INT NOT NULL');
        $this->addSql('ALTER TABLE quiz_user_answered ADD CONSTRAINT FK_A742BCF91E27F6BF FOREIGN KEY (question_id) REFERENCES quiz_question (id)');
        $this->addSql('CREATE INDEX IDX_A742BCF91E27F6BF ON quiz_user_answered (question_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_user_answered DROP FOREIGN KEY FK_A742BCF91E27F6BF');
        $this->addSql('DROP INDEX IDX_A742BCF91E27F6BF ON quiz_user_answered');
        $this->addSql('ALTER TABLE quiz_user_answered DROP question_id');
    }
}
