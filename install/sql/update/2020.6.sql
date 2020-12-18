--
-- Aggiornamento di LAMPSchool alla versione 2020.6
--


ALTER TABLE tbl_testisms
MODIFY idinvio char(100);



-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.6' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
