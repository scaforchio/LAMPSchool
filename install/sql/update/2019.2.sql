--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--


-- Aggiunto indice univoco per evitare doppio inserimento assenza in caso di simultaneità invio 
-- da parte di più terminali marcatempo

ALTER TABLE tbl_assenze ADD UNIQUE(idalunno, data);

ALTER TABLE tbl_docenti ADD 
collegamentowebex char(255);

ALTER TABLE tbl_valutazioniintermedie CHANGE giudizio giudizio varchar(1000) DEFAULT NULL;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('funzioni', 'notegenitori', 'yes', 'Visualizzazione note per i genitori', 'no|yes');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('funzioni', 'assenzegenitori', 'yes', 'Visualizzazione assenze per i genitori', 'no|yes');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('funzioni', 'stampacertificazioni', 'yes', 'Stampa certificazione competenze per i genitori', 'no|yes');



-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2019.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
