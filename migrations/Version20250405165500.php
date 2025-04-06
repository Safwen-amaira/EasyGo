<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405165500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recompensefidelite (id INT AUTO_INCREMENT NOT NULL, utilisateurfidelite_id INT DEFAULT NULL, description_recompense VARCHAR(255) DEFAULT NULL, points_requis INT NOT NULL, type_recompense VARCHAR(100) DEFAULT NULL, date_expiration DATETIME NOT NULL, bonus_points INT DEFAULT NULL, INDEX IDX_98B96FE5CC24AB2B (utilisateurfidelite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurfidelite (id INT AUTO_INCREMENT NOT NULL, nom_utilisateur VARCHAR(255) NOT NULL, points_accumules INT NOT NULL, total_trajets_effcetues INT NOT NULL, total_montant_depense DOUBLE PRECISION NOT NULL, date_derniere_mise_ajour DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recompensefidelite ADD CONSTRAINT FK_98B96FE5CC24AB2B FOREIGN KEY (utilisateurfidelite_id) REFERENCES utilisateurfidelite (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recompensefidelite DROP FOREIGN KEY FK_98B96FE5CC24AB2B');
        $this->addSql('DROP TABLE recompensefidelite');
        $this->addSql('DROP TABLE utilisateurfidelite');
    }
}
