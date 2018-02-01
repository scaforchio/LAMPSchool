--
-- Aggiornamento di LAMPSchool alla versione 2017.1
--



INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'indirizzomailfrom', 'lampschool@[server istituto]', 'Indirizzo mail di partenza delle mail', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'indirizzomailassenze', '[codicescuola]@istruzione.it', 'Indirizzo mail per comunicazione richieste astensione dal lavoro', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'mailresponsabilesostituzioni', '', 'Indirizzo mail del responsabile delle sostituzioni docenti assenti', '');


CREATE TABLE IF NOT EXISTS `tbl_richiesteferie` (
  `idrichiestaferie` int(11) NOT NULL,
  `iddocente` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(1000) NOT NULL DEFAULT '',
  `testomail` varchar(1000) NOT NULL DEFAULT '',
  `erroremail` boolean NULL DEFAULT NULL,
  `concessione` boolean NULL DEFAULT NULL,
  `oraultmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tbl_richiesteferie`
ADD PRIMARY KEY (`idrichiestaferie`);
ALTER TABLE `tbl_richiesteferie`
MODIFY `idrichiestaferie` int(11) NOT NULL AUTO_INCREMENT;
-- -------------------------------------
--
-- Struttura della tabella `tbl_assemblee`

CREATE TABLE `tbl_assemblee` (
  `idassemblea` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `datarichiesta` date NOT NULL,
  `dataassemblea` date NOT NULL,
  `orainizio` int(2) NOT NULL,
  `orafine` int(2) NOT NULL,
  `docenteconcedente1` int(11) NOT NULL,
  `docenteconcedente2` int(11) NOT NULL,
  `concesso1` tinyint(1) NOT NULL DEFAULT '0',
  `concesso2` tinyint(1) NOT NULL DEFAULT '0',
  `docenteautorizzante` int(11) NOT NULL,
  `autorizzato` tinyint(1) NOT NULL DEFAULT '0',
  `docentepresente1` int(11) NOT NULL,
  `docentepresente2` int(11) NOT NULL,
  `idmateria1` int(11) NOT NULL,
  `idmateria2` int(11) NOT NULL,
  `rappresentante1` int(11) NOT NULL,
  `rappresentante2` int(11) NOT NULL,
  `alunnopresidente` int(11) NOT NULL,
  `alunnosegretario` int(11) NOT NULL,
  `oratermine` time NOT NULL,
  `verbale` text NOT NULL,
  `visione_verbale` tinyint(1) NOT NULL DEFAULT '0',
  `commenti_verbale` text NOT NULL,
  `docente_visione` int(11) NOT NULL,
  `note` text NOT NULL,
  `consegna_verbale` tinyint(1) NOT NULL,
  `odg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tbl_assemblee`
ADD PRIMARY KEY (`idassemblea`);
ALTER TABLE `tbl_assemblee`
MODIFY `idassemblea` int(11) NOT NULL AUTO_INCREMENT;
--
-- Indexes for table `tbl_assemblee`
--



INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('tempo', 'distanza_assemblee', '3', 'Minimo numero di giorni tra richiesta assemblea e suo svolgimento', '1|2|3|4|5|6|7|8');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('tempo', 'numeromassimooreassemblea', '2', 'Massimo numero ore durata assemblea di classe', '1|2');



-- PER GESTIONE COLLOQUI CON LIMITE E SOSPENSIONI


ALTER TABLE tbl_docenti
ADD nummaxcolloqui tinyint NOT NULL DEFAULT 5;


CREATE TABLE IF NOT EXISTS `tbl_sospensionicolloqui` (
  `idsospensionecolloqui` int(11) NOT NULL,
  `data` date NOT NULL,
  `note` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `tbl_sospensionicolloqui`
ADD PRIMARY KEY (`idsospensionecolloqui`);
ALTER TABLE `tbl_sospensionicolloqui`
MODIFY `idsospensionecolloqui` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE tbl_classi
ADD `rappresentante1` int(11) NOT NULL DEFAULT '0';

ALTER TABLE tbl_classi
ADD `rappresentante2` int(11) NOT NULL DEFAULT '0';

INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (34, 'alupassalu00', 'Gent.mo alunno', 'Formula prima del nome dell''alunno', '');
INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (35, 'alupassalu01', 'Con le credenziali qui fornite potrai  accedere allâ€™area riservata agli alunni del Registro Online LAMPSchool.
In tale area potrai visualizzare i dati relativi al tuo percorso scolastico: assenze, ritardi, uscite anticipate, valutazioni, note, argomenti delle lezioni, ecc. I rappresentanti di classe potranno inoltre gestire l''iter delle assemblee di classe.', 'Testo prima di comunicazione password per alunno.', '');
INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (36, 'alupassalu02', 'Ricorda che i dati di accesso sono personali e sarai ritenuto direttamente responsabile di quello che viene fatto con il tuo account.', 'Testo dopo comunicazione password per alunni.', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc01', 'NONCOR', 'Primo giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc02', 'SPPCCOR', 'Secondo giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc03', 'PCOCOR', 'Terzo giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc04', 'NONSEMCOR', 'Quarto giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc05', 'QUACOR', 'Quinto giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc06', 'APPCOR', 'Sesto giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc07', 'ABBCOR', 'Settimo giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc08', 'GENCOR', 'Ottavo giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc09', 'MOLCOR', 'Nono giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'gc10', 'ESEMPL', 'Decimo giudizio abbreviato per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp01', 'Non corretto', 'Primo giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp02', 'Spesso poco corretto', 'Secondo giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp03', 'Poco corretto', 'Terzo giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp04', 'Non sempre corretto', 'Quarto giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp05', 'Quasi corretto', 'Quinto giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp06', 'Appena corretto', 'Sesto giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp07', 'Abbastanza corretto', 'Settimo giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp08', 'Generalmente corretto', 'Ottavo giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp09', 'Molto corretto', 'Nono giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('voti', 'giudcomp10', 'Esemplare', 'Decimo giudizio per valutazione di comportamento (inserire NULL per non utilizzarlo)', '');

UPDATE tbl_materie set tipovalutazione='CU' where idmateria='-1';
--

--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2017.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
