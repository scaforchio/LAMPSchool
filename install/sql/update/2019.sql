--
-- Aggiornamento di LAMPSchool alla versione 2019
--
ALTER TABLE tbl_alunni CHANGE autentrata autentrata VARCHAR(100) NULL DEFAULT NULL, 
                       CHANGE autuscita autuscita VARCHAR(100) NULL DEFAULT NULL;
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2019.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
