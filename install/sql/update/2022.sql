--
-- Aggiornamento di LAMPSchool alla versione 2022
--

ALTER TABLE tbl_alunni CHANGE indirizzo indirizzo VARCHAR(60); 
ALTER TABLE tbl_richiesteferie ADD orariorichiesta CHAR(5);

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2022' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
