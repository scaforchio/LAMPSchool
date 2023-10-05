INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'pwdreset', 'no','Reset password autonomo', 'yes|no');

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE