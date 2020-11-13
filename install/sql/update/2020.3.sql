--
-- Aggiornamento di LAMPSchool alla versione 2020.3
--

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'tempomassimosessione', '60', 'Tempo logout in minuti dopo ultima azione', '5|10|15|20|30|40|50|60|120|1440');
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.3' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
