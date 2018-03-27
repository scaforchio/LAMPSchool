--
-- Aggiornamento di LAMPSchool alla versione 2017.2
--

ALTER TABLE tbl_esesiti CHANGE mediascramm mediascrcolloq DECIMAL(4,2) NULL DEFAULT NULL;

INSERT INTO tbl_parametri (idparametro, gruppo, parametro, valore, descrizione, valoriammessi) VALUES (NULL, 'voti', 'solovotiinteri', 'no', 'Stabilisce se le valutazioni delle verifiche devono essere solo intere.', 'no|yes');

--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2017.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
