--
-- Aggiornamento di LAMPSchool alla versione 2020.9
--


INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'mailpermdopoauto', 'yes', 'Invio della mail per richiesta permesso a protocollo dopo autorizzazione (yes/no).', 'yes|no');

ALTER TABLE tbl_richiesteferie MODIFY testomail varchar(2000) NULL;


-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.10' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
