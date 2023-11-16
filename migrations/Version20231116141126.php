<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116141126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_vehicule_id INTEGER NOT NULL, id_member_id INTEGER NOT NULL, date_depart DATETIME NOT NULL, date_fin DATETIME NOT NULL, prix_total INTEGER NOT NULL, date_enregistrement DATETIME NOT NULL, CONSTRAINT FK_6EEAA67D5258F8E6 FOREIGN KEY (id_vehicule_id) REFERENCES vehicule (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6EEAA67DF5A26FD9 FOREIGN KEY (id_member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D5258F8E6 ON commande (id_vehicule_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF5A26FD9 ON commande (id_member_id)');
        $this->addSql('CREATE TABLE member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pseudo VARCHAR(20) NOT NULL, mdp VARCHAR(60) NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, email VARCHAR(50) NOT NULL, statut INTEGER NOT NULL, date_enregistrement DATETIME NOT NULL, civilite INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE vehicule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(50) NOT NULL, description CLOB NOT NULL, photo VARCHAR(200) NOT NULL, prix_journalier INTEGER NOT NULL, date_enregistrement DATETIME NOT NULL, available VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
