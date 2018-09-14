--
-- Aggiornamento di LAMPSchool alla versione 2018
--

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'sitoinmanutenzione', 'no', 'Blocco accesso per genitori e docenti non staff', 'yes|no');


--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2018.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
