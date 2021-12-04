<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211204171416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile_picture_user (profile_picture_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_85DB12A1292E8AE2 (profile_picture_id), INDEX IDX_85DB12A1A76ED395 (user_id), PRIMARY KEY(profile_picture_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profile_picture_user ADD CONSTRAINT FK_85DB12A1292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES profile_picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_picture_user ADD CONSTRAINT FK_85DB12A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES profile_picture (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649292E8AE2 ON user (profile_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE profile_picture_user');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649292E8AE2');
        $this->addSql('DROP INDEX IDX_8D93D649292E8AE2 ON user');
    }
}
