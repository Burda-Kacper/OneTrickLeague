<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211121212327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz_question_quiz_saved (quiz_question_id INT NOT NULL, quiz_saved_id INT NOT NULL, INDEX IDX_C5B0A0C3101E51F (quiz_question_id), INDEX IDX_C5B0A0C1D159D99 (quiz_saved_id), PRIMARY KEY(quiz_question_id, quiz_saved_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz_question_quiz_saved ADD CONSTRAINT FK_C5B0A0C3101E51F FOREIGN KEY (quiz_question_id) REFERENCES quiz_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_question_quiz_saved ADD CONSTRAINT FK_C5B0A0C1D159D99 FOREIGN KEY (quiz_saved_id) REFERENCES quiz_saved (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE quiz_question_quiz_saved');
    }
}
