<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114153201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__member AS SELECT id, pseudo, mdp, nom, prenom, email, statut, date_enregistrement, civilite FROM member');
        $this->addSql('DROP TABLE member');
        $this->addSql('CREATE TABLE member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pseudo VARCHAR(20) NOT NULL, mdp VARCHAR(60) NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, email VARCHAR(50) NOT NULL, statut INTEGER NOT NULL, date_enregistrement DATETIME NOT NULL, civilite INTEGER NOT NULL)');
        $this->addSql('INSERT INTO member (id, pseudo, mdp, nom, prenom, email, statut, date_enregistrement, civilite) SELECT id, pseudo, mdp, nom, prenom, email, statut, date_enregistrement, civilite FROM __temp__member');
        $this->addSql('DROP TABLE __temp__member');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__member AS SELECT id, pseudo, mdp, nom, prenom, email, statut, date_enregistrement, civilite FROM member');
        $this->addSql('DROP TABLE member');
        $this->addSql('CREATE TABLE member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pseudo VARCHAR(20) NOT NULL, mdp VARCHAR(60) NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, email VARCHAR(50) NOT NULL, statut INTEGER NOT NULL, date_enregistrement DATETIME NOT NULL, civilite CLOB NOT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO member (id, pseudo, mdp, nom, prenom, email, statut, date_enregistrement, civilite) SELECT id, pseudo, mdp, nom, prenom, email, statut, date_enregistrement, civilite FROM __temp__member');
        $this->addSql('DROP TABLE __temp__member');
    }
}
