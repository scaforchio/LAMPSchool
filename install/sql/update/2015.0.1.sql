-- 
-- Aggiornamento di LAMPSchool alla versione 2015.0.1
--


ALTER TABLE tbl_alunni CHANGE telcel telcel VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE tbl_diariocl CHANGE iddiariocl iddiariocl INT(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS tbl_collegamenti (
  idcollegamento int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  descrizione varchar(80) NOT NULL,
  link varchar(255) NOT NULL,
  destinatari varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'gestcentrautorizz', 'no', 'Gestione ritardi e uscite anticipate solo da staff (yes/no).', 'yes|no');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'gesttimbrature', 'no','Gestione rilevazione entrate e uscite tramite badge (yes/no).', 'yes|no');
ALTER TABLE tbl_alunni ADD firmapropria tinyint(1) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS tbl_entrateclassi (
  identrataclasse int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  idclasse int(11) NOT NULL,
  data date NOT NULL,
  ora time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_assenze ADD dataammonizione date DEFAULT NULL;
ALTER TABLE tbl_ritardi ADD dataammonizione date DEFAULT NULL;
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'maxritardogiust', '2','Massimo ritardo nella presentazione delle giustifiche di assenze e ritardi.', '1|2|3|4|5|6');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'modocron', 'acc','Modo di gestione deii lavori automatici quitidiani (acc=primo accesso - cron=esecuzione script cron.', 'acc|cron');

INSERT INTO tbl_testi (nometesto, valore, spiegazione, possibilivalori) VALUES ('ammonizmancgiust', 'L''alunno [alunno] Ã¨ ammonito ai sensi del regolamento disciplinare.', 'Dicitura del provvedimento disciplinare per mancata giustifica di assenza o ritardo entro i termini', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'visvotocomp', 'yes','Visualizzazione del voto medio di comportamento per i tutor.', 'yes|no');

ALTER TABLE tbl_alunni ADD autorizzazioni TEXT DEFAULT NULL;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'nomefilelog', md5(rand()),'Nome file di log.', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'ritardobreve', '10','Massimo numero di minuti per ritardo breve.', '');

CREATE TABLE IF NOT EXISTS tbl_sospinviosms (
  idsospinviosms int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  datasosp date NOT NULL,
  ultimamodifica timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2015.0.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
