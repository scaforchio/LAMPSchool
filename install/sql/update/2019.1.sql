--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--
ALTER TABLE tbl_alunni CHANGE autentrata autentrata VARCHAR(100) NULL DEFAULT NULL, 
                       CHANGE autuscita autuscita VARCHAR(100) NULL DEFAULT NULL;


INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'emailgestbadge', '', 'Email del gestore dei badge per le timbrature','');

CREATE TABLE IF NOT EXISTS tbl_torlist (
  idtorlist int(11),
  indirizzo char(60),
  oraultmod timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_torlist
ADD PRIMARY KEY (idtorlist), ADD KEY indirizzo (indirizzo);

ALTER TABLE tbl_torlist
MODIFY idtorlist int(11) AUTO_INCREMENT;

ALTER TABLE tbl_utenti
ADD token char(5);

ALTER TABLE tbl_utenti
ADD coordinatetoken char(10);

ALTER TABLE tbl_utenti
ADD schematoken char(50);

ALTER TABLE tbl_utenti
ADD modoinviotoken char(1);

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2019.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
