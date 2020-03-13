--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--


-- Aggiunto indice univoco per evitare doppio inserimento assenza in caso di simultaneità invio 
-- da parte di più terminali marcatempo

ALTER TABLE tbl_assenze ADD UNIQUE(idalunno, data);

ALTER TABLE tbl_docenti ADD 
collegamentowebex char(255);



-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2019.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
