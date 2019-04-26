--
-- Aggiornamento di LAMPSchool alla versione 2018.1
--

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'sitoinmanutenzione', 'no', 'Blocco accesso per genitori e docenti non staff', 'yes|no');

ALTER TABLE tbl_richiesteferie ADD numerogiorni TINYINT NOT NULL DEFAULT 0 AFTER oraultmod, 
                               ADD orepermessobreve TINYINT NOT NULL DEFAULT 0 AFTER numerogiorni;

CREATE TABLE IF NOT EXISTS tbl_autorizzazioniuscite (
  idalunno int(11) DEFAULT '0',
  data date DEFAULT NULL,
  orauscita time DEFAULT NULL,
  idautorizzazioneuscita int(11),
  iddocenteautorizzante int(11) DEFAULT NULL,
  testoautorizzazione varchar(500) DEFAULT NULL,
  oraultmod timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_autorizzazioniuscite
ADD PRIMARY KEY (idautorizzazioneuscita);

ALTER TABLE tbl_autorizzazioniuscite
MODIFY idautorizzazioneuscita int(11) AUTO_INCREMENT;

ALTER TABLE tbl_annotazioni
ADD visibilitagenitori boolean DEFAULT 0;

ALTER TABLE tbl_richiesteferie CHANGE testomail testomail 
VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci 
NULL DEFAULT NULL;

ALTER TABLE tbl_assemblee CHANGE docenteconcedente1 docenteconcedente1 INT(11) NULL DEFAULT '0', CHANGE docenteconcedente2 docenteconcedente2 INT(11) NULL DEFAULT '0', CHANGE docenteautorizzante docenteautorizzante INT(11) NULL DEFAULT '0', CHANGE docentepresente1 docentepresente1 INT(11) NULL DEFAULT '0', CHANGE docentepresente2 docentepresente2 INT(11) NULL DEFAULT '0', CHANGE rappresentante1 rappresentante1 INT(11) NULL DEFAULT '0', CHANGE rappresentante2 rappresentante2 INT(11) NULL DEFAULT '0', CHANGE alunnopresidente alunnopresidente INT(11) NULL DEFAULT '0', CHANGE alunnosegretario alunnosegretario INT(11) NULL DEFAULT '0', CHANGE docente_visione docente_visione INT(11) NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS tbl_consorientativi (
  idalunno int(11),
  consiglioorientativo text,
   oraultmod timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_annotazioni
ADD visibilitaalunni boolean DEFAULT 0;
 
UPDATE tbl_annotazioni set visibilitagenitori=0 where isnull(visibilitagenitori);
UPDATE tbl_annotazioni set visibilitaalunni=0 where isnull(visibilitaalunni);

ALTER TABLE tbl_assemblee ADD rapportoperdirigente text AFTER commenti_verbale;

CREATE TABLE IF NOT EXISTS tbl_recuperipermessi (
  idrecupero int(11),
  iddocente int(11),
  datarecupero date DEFAULT NULL,
  numeroore int(1),
  motivo varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_recuperipermessi
ADD PRIMARY KEY (idrecupero);

ALTER TABLE tbl_recuperipermessi
MODIFY idrecupero int(11) AUTO_INCREMENT;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'codicevicario', '', 'Codice del docente con funzioni di vicario','');

ALTER TABLE tbl_classi
ADD aprifila1 int(11) DEFAULT 0,
ADD aprifila2 int(11) DEFAULT 0,
ADD chiudifila1 int(11) DEFAULT 0,
ADD chiudifila2 int(11) DEFAULT 0;

-- 26/01/2019
ALTER TABLE tbl_alunni
ADD autuscitaantclasse tinyint(1) DEFAULT '0' AFTER firmapropria;


ALTER TABLE tbl_docenti ADD gestoremoodle tinyint(1) DEFAULT '0';

ALTER TABLE tbl_richiesteferie
ADD annullata tinyint NOT NULL DEFAULT 0;


ALTER TABLE tbl_entrateclassi
ADD idannotazione int(11) NOT NULL DEFAULT 0;
--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2018.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
