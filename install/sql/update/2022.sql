--
-- Aggiornamento di LAMPSchool alla versione 2021.1
--

ALTER TABLE tbl_alunni CHANGE indirizzo indirizzo VARCHAR(60); 

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2022' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
