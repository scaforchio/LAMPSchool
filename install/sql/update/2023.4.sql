ALTER TABLE `tbl_alunni` 
    ADD `telproprio` VARCHAR(20) NOT NULL DEFAULT '' after `idgrupporitardo`,
    ADD `mailpropria` VARCHAR(100) NOT NULL DEFAULT '' after `telproprio`;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'appuntamentoonline', 'no','Colloqui mattutini online', 'yes|no');

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023.4' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE