--
-- Aggiornamento di LAMPSchool alla versione 2020.6
--


ALTER TABLE tbl_valutazioniobiettivi
ADD periodo char(1);



-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.11' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
