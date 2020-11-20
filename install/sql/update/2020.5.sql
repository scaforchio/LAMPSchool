--
-- Aggiornamento di LAMPSchool alla versione 2020.5
--
ALTER TABLE tbl_parametri ADD UNIQUE (parametro);

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.5' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
