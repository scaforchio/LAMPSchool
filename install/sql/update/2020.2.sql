--
-- Aggiornamento di LAMPSchool alla versione 2020.2
--

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('funzioni', 'giustificauscite', 'no', 'Richiesta giustificazione per uscite anticipate', 'yes|no');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('funzioni', 'giustificaasslezione', 'no', 'Richiesta giustificazione per assenze alle singole lezioni', 'yes|no');

ALTER TABLE tbl_asslezione ADD giustifica tinyint(1) DEFAULT '0';
ALTER TABLE tbl_asslezione ADD iddocentegiust int(11) DEFAULT NULL;
ALTER TABLE tbl_asslezione ADD datagiustifica date DEFAULT NULL;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
