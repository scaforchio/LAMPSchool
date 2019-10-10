--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--
ALTER TABLE tbl_alunni CHANGE autentrata autentrata VARCHAR(100) NULL DEFAULT NULL, 
                       CHANGE autuscita autuscita VARCHAR(100) NULL DEFAULT NULL;


INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'emailgestbadge', '', 'Email del gestore dei badge per le timbrature','');

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2019.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
