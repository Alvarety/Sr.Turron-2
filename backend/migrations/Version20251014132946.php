<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014132946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pago (id INT AUTO_INCREMENT NOT NULL, pedido_id INT NOT NULL, metodo_pago VARCHAR(50) NOT NULL, monto NUMERIC(10, 2) NOT NULL, fecha_pago DATETIME NOT NULL, estado_pago VARCHAR(20) NOT NULL, INDEX IDX_F4DF5F3E4854653A (pedido_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedido (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, fechapedido DATETIME NOT NULL, estado VARCHAR(255) NOT NULL, total NUMERIC(10, 2) NOT NULL, direccion_envio VARCHAR(255) DEFAULT NULL, INDEX IDX_C4EC16CEDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pedido_detalle (id INT AUTO_INCREMENT NOT NULL, pedido_id INT NOT NULL, producto_id INT NOT NULL, cantidad INT NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, subtotal DOUBLE PRECISION NOT NULL, INDEX IDX_E240F45E4854653A (pedido_id), INDEX IDX_E240F45E7645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, precio NUMERIC(10, 2) NOT NULL, stock INT NOT NULL, imagen_url VARCHAR(255) DEFAULT NULL, activo TINYINT(1) NOT NULL, INDEX IDX_A7BB06153397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pago ADD CONSTRAINT FK_F4DF5F3E4854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('ALTER TABLE pedido ADD CONSTRAINT FK_C4EC16CEDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE pedido_detalle ADD CONSTRAINT FK_E240F45E4854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('ALTER TABLE pedido_detalle ADD CONSTRAINT FK_E240F45E7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB06153397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pago DROP FOREIGN KEY FK_F4DF5F3E4854653A');
        $this->addSql('ALTER TABLE pedido DROP FOREIGN KEY FK_C4EC16CEDB38439E');
        $this->addSql('ALTER TABLE pedido_detalle DROP FOREIGN KEY FK_E240F45E4854653A');
        $this->addSql('ALTER TABLE pedido_detalle DROP FOREIGN KEY FK_E240F45E7645698E');
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB06153397707A');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE pago');
        $this->addSql('DROP TABLE pedido');
        $this->addSql('DROP TABLE pedido_detalle');
        $this->addSql('DROP TABLE producto');
    }
}
