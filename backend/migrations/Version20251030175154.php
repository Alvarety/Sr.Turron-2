<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030175154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comentario (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, resena_id INT NOT NULL, texto LONGTEXT NOT NULL, fecha DATETIME NOT NULL, INDEX IDX_4B91E702DB38439E (usuario_id), INDEX IDX_4B91E70219764015 (resena_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resena (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, producto_id INT NOT NULL, comentario LONGTEXT NOT NULL, puntuacion DOUBLE PRECISION NOT NULL, fecha DATETIME NOT NULL, INDEX IDX_50A7E40ADB38439E (usuario_id), INDEX IDX_50A7E40A7645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E702DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E70219764015 FOREIGN KEY (resena_id) REFERENCES resena (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40ADB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40A7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E702DB38439E');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E70219764015');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40ADB38439E');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40A7645698E');
        $this->addSql('DROP TABLE comentario');
        $this->addSql('DROP TABLE resena');
    }
}
