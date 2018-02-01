- 
-- Aggiornamento di LAMPSchool alla versione 2017
--

alter table tbl_classi add idmoodle char(5) default '';

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('moodle', 'urlmoodle', 'http://[url sito moodle]', 'Url del sito moodle per operazioni di sincronizzazione', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('moodle', 'tokenservizimoodle', '', 'Token per accesso web service moodle di interfaccia con LAMPSchool', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'giustificauscite', 'yes', 'Indica se è prevista la giustifica delle uscite anticipate', 'yes|no');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('tempo', 'datafinecolloqui', '2016-09-15', 'Data di fine colloqui', 'data');

INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (31, 'passmoodlealu00', 'Gent.mo genitore dell''alunno', 'Formula prima del nome dell''alunno', '');
INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (32, 'passmoodlealu01', 'Con le credenziali qui fornite potrÃ  accedere allâ€™area riservata ai genitori del Registro Online LAMPSchool per lâ€™A.S. 2017-2018.
In tale area, raggiungibile con il link â€™Registro On Line ITTâ€™, potrÃ  visualizzare i dati relativi al percorso scolastico di suo figlio: assenze, ritardi, uscite anticipate, valutazioni, note, comunicazioni della scuola, pagelle, argomenti delle lezioni, ecc.', 'Testo prima di comunicazione password per alunno.', '');
INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (33, 'passmoodlealu02', 'Sperando di averle fatto cosa gradita si coglie l''occasione per salutarLa cordialmente.', 'Testo dopo comunicazione password per alunni.', '');


ALTER TABLE `tbl_paramcomunicazpers`
MODIFY `idparamcomunicazpers` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE tbl_usciteanticipate
    ADD `giustifica` tinyint(1) NOT NULL;
ALTER TABLE tbl_usciteanticipate
ADD `iddocentegiust` int(11) DEFAULT NULL;
ALTER TABLE tbl_usciteanticipate
ADD `datagiustifica` date DEFAULT NULL;
ALTER TABLE tbl_usciteanticipate
ADD`dataammonizione` date DEFAULT NULL;




ALTER TABLE tbl_utenti ADD ultimoaccessoapp BIGINT NOT NULL AFTER passprecedenti;

ALTER TABLE tbl_sms CHANGE idinvio idinvio CHAR(100) NOT NULL;

--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2017' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
