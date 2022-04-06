--
-- Aggiornamento di LAMPSchool alla versione 2021.1
--

ALTER TABLE tbl_giornatacolloqui ADD attiva boolean default false;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2021.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
