<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200708113929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album ADD dt_change DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784189FCD471');
        $this->addSql('DROP INDEX IDX_14B784189FCD471 ON photo');
        $this->addSql('ALTER TABLE photo CHANGE album_id album_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784189FCD471 FOREIGN KEY (album_id_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_14B784189FCD471 ON photo (album_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP dt_change');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784189FCD471');
        $this->addSql('DROP INDEX IDX_14B784189FCD471 ON photo');
        $this->addSql('ALTER TABLE photo CHANGE album_id_id album_id INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784189FCD471 FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_14B784189FCD471 ON photo (album_id)');
    }
}
