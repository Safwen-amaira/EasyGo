<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502151316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE type_contrat type_contrat VARCHAR(255) DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE easygo_users CHANGE totp_secret totp_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recompense_fidelite CHANGE statut statut VARCHAR(255) DEFAULT NULL, CHANGE date_expiration date_expiration DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE lieu_escale lieu_escale VARCHAR(100) DEFAULT NULL, CHANGE type_handicap type_handicap VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE trip CHANGE return_time return_time TIME DEFAULT NULL, CHANGE contribution contribution NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE type_recompense CHANGE categorie categorie VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE totp_secret totp_secret VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateurfidelite CHANGE total_montant total_montant DOUBLE PRECISION DEFAULT NULL, CHANGE nom_utilisateur nom_utilisateur VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicule CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE content content VARCHAR(255) DEFAULT NULL, CHANGE color color VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(255) DEFAULT \'NULL\', CHANGE type_contrat type_contrat VARCHAR(255) DEFAULT \'NULL\', CHANGE date_fin date_fin DATE DEFAULT \'NULL\', CHANGE description description VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE easygo_users CHANGE totp_secret totp_secret VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE recompense_fidelite CHANGE statut statut VARCHAR(255) DEFAULT \'NULL\', CHANGE date_expiration date_expiration DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reservation CHANGE lieu_escale lieu_escale VARCHAR(100) DEFAULT \'NULL\', CHANGE type_handicap type_handicap VARCHAR(50) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE trip CHANGE return_time return_time TIME DEFAULT \'NULL\', CHANGE contribution contribution NUMERIC(10, 2) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE type_recompense CHANGE categorie categorie VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE totp_secret totp_secret VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE utilisateurfidelite CHANGE total_montant total_montant DOUBLE PRECISION DEFAULT \'NULL\', CHANGE nom_utilisateur nom_utilisateur VARCHAR(100) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE vehicule CHANGE image image VARCHAR(255) DEFAULT \'NULL\', CHANGE content content VARCHAR(255) DEFAULT \'NULL\', CHANGE color color VARCHAR(255) DEFAULT \'NULL\'');
    }
}
