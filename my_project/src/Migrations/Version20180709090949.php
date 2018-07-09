<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180709090949 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hashtag CHANGE hastag_name hashtag_name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE post_hashtag DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE post_hashtag ADD PRIMARY KEY (hashtag_id, post_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hashtag CHANGE hashtag_name hastag_name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE post_hashtag DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE post_hashtag ADD PRIMARY KEY (post_id, hashtag_id)');
    }
}
