--
-- Aggiornamento di LAMPSchool alla versione 2021
--


ALTER TABLE tbl_valutazioniobiettivi CHANGE perdiodo periodo char(1);

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2021' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
