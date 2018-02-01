-- 
-- Aggiornamento di LAMPSchool alla versione 2015.1
--

ALTER TABLE tbl_sms ADD sottotipo CHAR(10) NULL DEFAULT NULL AFTER tipo;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'gestcentrassenze', 'no', 'Gestione assenze solo da staff', 'yes|no');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'valutazionepercompetenze', 'yes', 'Gestione della valutazione per competenze', 'yes|no');


INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (24, 'passalu03', '(Da restituire alla segreteria)', 'Dicitura a chi restituire il tagliando', '');

INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (25, 'passalu04', 'Il/la sottoscritto/a genitore dell''alunno/a', 'Formula della ricevuta prima del nome dell''alunno', '');

INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (26, 'passalu05', ', dichiara di aver ricevuto i dati di accesso al registro online LAMPschool.', 'Formula della ricevuta dopo il nome dell''alunno', '');


INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (27, 'testoverbesa01', 'Valutazone dei risultati degli esami e decisioni conseguenti.

Il giorno [giorno] del mese di [mese] dell''anno [anno] alle ore [orainizio], sotto la presidenza del prof. [presidente], Presidente della Commissione, si riunisce la sottocommissione [commissione], composta dai professori [elenco docenti] per procedere alla valutazione dei risultati degli esami ai sensi delle norme vigenti.
Funge da segretario il prof. [segretario].
Sulla base delle risultanze complessive del giudizio di idoneitÃ , delle prove scritte - inclusa quella nazionale - e del colloquio pluridisciplinare, la sottocommissione esaminatrice che, comunque, Ã¨ chiamata ad operare collegialmente nella correzione degli elaborati e nello svolgimento dei colloqui, assegna ad ogni candidato il voto finale.
', 'Prima parte verbale esame di stato terza media', '');
INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (28, 'testoverbesa02', 'Tale valutazione, in tutti i casi in cui risulta positiva, si conclude con l''attribuzione della valutazione espressa in decimi, che viene segnata a fianco di ogni nominativo e con la verifica ed eventuale integrazione del consiglio orientativo.
I risultati da sottoporre alla ratifica della Commissione plenaria sono i seguenti:', 'Seconda parte verbale esame di stato terza media', '');
INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (29, 'testoverbesa03', '[omissis]', 'Terza parte verbale esame di stato terza media', '');

INSERT INTO tbl_testi (idtesto, nometesto, valore, spiegazione, possibilivalori) VALUES (30, 'testoverbesa04', 'I risultati decisi "a maggioranza" vengono evidenziati con una (M) segnata tra parentesi a fianco del nome o del giudizio relativo. Tutti gli altri risultati privi di indicazione sono stati decisi all''unanimitÃ .

Dalle discussioni e dalle osservazioni della sottocommissione Ã¨ inoltre emerso quanto segue:


La seduta Ã¨ tolta alle ore [orafine] dopo la lettura e l''approvazione del presente verbale.

La sottocommissione [commissione]:', 'Quarta parte del verbale esame di stato terza media', '');


INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'numeroritardisms', '0', 'Se il valore impostato è diverso da 0 gli sms saranno inviati solo per numero ritardi maggiori del valore specificato.', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'indirizzo_scuola', 'Via aaaaaaaaaa, n. 0 - 00000 Comune (PR) - Tel. 0000000000000 - Email: email@server.it','Indirizzo completo della scuola.', '');


ALTER TABLE tbl_alunni ADD idclasseesame INT(11) NULL DEFAULT '0' AFTER idclasse;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_esami3m`
--

CREATE TABLE IF NOT EXISTS `tbl_esami3m` (
  `idesame` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `datascrutinio` date NOT NULL,
  `stato` char(1) NOT NULL,
  `luogoscrutinio` varchar(100) NOT NULL DEFAULT '',
  `dataverbale` date NOT NULL,
  `datastampa` date NOT NULL,
  `ultimamodifica` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idcommissione` int(11) NOT NULL,
  `testo1` longtext NOT NULL,
  `testo2` longtext NOT NULL,
  `testo3` longtext NOT NULL,
  `testo4` longtext NOT NULL,
  `orainizio` char(5) NOT NULL,
  `orafine` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_escommissioni`
--

CREATE TABLE IF NOT EXISTS `tbl_escommissioni` (
  `idescommissione` int(11) NOT NULL,
  `denominazione` char(30) NOT NULL,
  `nomepresidente` char(30) NOT NULL,
  `cognomepresidente` char(30) NOT NULL,
  `idsegretario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_escompcommissioni`
--

CREATE TABLE IF NOT EXISTS `tbl_escompcommissioni` (
  `idescompcommissione` int(11) NOT NULL,
  `idcommissione` int(11) NOT NULL,
  `iddocente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_esesiti`
--

CREATE TABLE IF NOT EXISTS `tbl_esesiti` (
  `idalunno` int(11) NOT NULL,
  `votoammissione` int(2) NOT NULL,
  `votom1` int(2) NOT NULL,
  `votom2` int(2) NOT NULL,
  `votom3` int(2) NOT NULL,
  `votom4` int(2) NOT NULL,
  `votom5` int(2) NOT NULL,
  `votom6` int(2) NOT NULL,
  `votom7` int(2) NOT NULL,
  `votom8` int(2) NOT NULL,
  `votom9` int(2) NOT NULL,
  `votoorale` int(2) NOT NULL,
  `mediascramm` decimal(4,2) NOT NULL,
  `mediafinale` decimal(4,2) NOT NULL,
  `votofinale` int(2) NOT NULL,
  `scarto` decimal(2,2) NOT NULL,
  `idesesiti` int(11) NOT NULL,
  `consorientcons` text NOT NULL,
  `consorientcomm` text NOT NULL,
  `provasceltam1` char(6) NOT NULL,
  `provasceltam2` char(6) NOT NULL,
  `provasceltam3` char(6) NOT NULL,
  `provasceltam4` char(6) NOT NULL,
  `provasceltam5` char(6) NOT NULL,
  `provasceltam6` char(6) NOT NULL,
  `provasceltam7` char(6) NOT NULL,
  `provasceltam8` char(6) NOT NULL,
  `provasceltam9` char(6) NOT NULL,
  `criteri1` text NOT NULL,
  `criteri2` text NOT NULL,
  `criteri3` text NOT NULL,
  `criteri4` text NOT NULL,
  `criteri5` text NOT NULL,
  `criteri6` text NOT NULL,
  `criteri7` text NOT NULL,
  `criteri8` text NOT NULL,
  `criteri9` text NOT NULL,
  `votopniita` smallint(6) NOT NULL,
  `votopnimat` smallint(6) NOT NULL,
  `tracciacolloquio` text NOT NULL,
  `datacolloquio` date NOT NULL,
  `giudiziocolloquio` text NOT NULL,
  `giudiziocomplessivo` text NOT NULL,
  `lode` tinyint(1) NOT NULL,
  `unanimita` TINYINT(1) NOT NULL,
  `ammissioneterza` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_esmaterie`
--

CREATE TABLE IF NOT EXISTS `tbl_esmaterie` (
  `idesmaterie` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `m1s` char(5) NOT NULL,
  `m1e` char(30) NOT NULL,
  `m1m` tinyint(4) NOT NULL,
  `m2s` char(5) NOT NULL,
  `m2e` char(30) NOT NULL,
  `m2m` tinyint(4) NOT NULL,
  `m3s` char(5) NOT NULL,
  `m3e` char(30) NOT NULL,
  `m3m` tinyint(4) NOT NULL,
  `m4s` char(5) NOT NULL,
  `m4e` char(30) NOT NULL,
  `m4m` tinyint(4) NOT NULL,
  `m5s` char(5) NOT NULL,
  `m5e` char(30) NOT NULL,
  `m5m` tinyint(4) NOT NULL,
  `m6s` char(5) NOT NULL,
  `m6e` char(30) NOT NULL,
  `m6m` tinyint(4) NOT NULL,
  `m7s` char(5) NOT NULL,
  `m7e` char(30) NOT NULL,
  `m7m` tinyint(4) NOT NULL,
  `m8s` char(5) NOT NULL,
  `m8e` char(30) NOT NULL,
  `m8m` tinyint(4) NOT NULL,
  `m9s` char(5) NOT NULL,
  `m9e` char(30) NOT NULL,
  `m9m` tinyint(4) NOT NULL,
  `num2lin` smallint(6) NOT NULL,
  `numpni` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indexes for table `tbl_esami3m`
--
ALTER TABLE `tbl_esami3m`
ADD PRIMARY KEY (`idesame`);

--
-- Indexes for table `tbl_escommissioni`
--
ALTER TABLE `tbl_escommissioni`
ADD PRIMARY KEY (`idescommissione`);

--
-- Indexes for table `tbl_escompcommissioni`
--
ALTER TABLE `tbl_escompcommissioni`
ADD PRIMARY KEY (`idescompcommissione`);

--
-- Indexes for table `tbl_esesiti`
--
ALTER TABLE `tbl_esesiti`
ADD PRIMARY KEY (`idesesiti`);

--
-- Indexes for table `tbl_esmaterie`
--
ALTER TABLE `tbl_esmaterie`
ADD PRIMARY KEY (`idesmaterie`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_esami3m`
--
ALTER TABLE `tbl_esami3m`
MODIFY `idesame` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_escommissioni`
--
ALTER TABLE `tbl_escommissioni`
MODIFY `idescommissione` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_escompcommissioni`
--
ALTER TABLE `tbl_escompcommissioni`
MODIFY `idescompcommissione` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_esesiti`
--
ALTER TABLE `tbl_esesiti`
MODIFY `idesesiti` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_esmaterie`
--
ALTER TABLE `tbl_esmaterie`
MODIFY `idesmaterie` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'passwordesame', '', 'Password per accedere a funzioni d''esame.', '');


--
-- Struttura della tabella `tbl_goindirizzo`
--

CREATE TABLE IF NOT EXISTS `tbl_goindirizzo` (
  `idindirizzo` int(11) NOT NULL,
  `idsettore` int(11) NOT NULL,
  `denominazione` text NOT NULL,
  `codsidi` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `tbl_goindirizzo`
--

INSERT INTO `tbl_goindirizzo` (`idindirizzo`, `idsettore`, `denominazione`, `codsidi`) VALUES
  (1, 1, 'Classico', ''),
  (2, 2, 'Scientifico', ''),
  (3, 2, 'Scientifico - Opzione Scienze Applicate', ''),
  (4, 2, 'Scientifico - Sezione ad Indirizzo Sportivo', ''),
  (5, 3, 'Linguistico', ''),
  (6, 4, 'Artistico Nuovo Ordinamento - Biennio Comune', ''),
  (7, 5, 'Scienze Umane', ''),
  (8, 5, 'Scienze Umane - Opz. Economico Sociale', ''),
  (9, 6, 'Musicale e Coreutico - Sez. Musicale', ''),
  (10, 6, 'Musicale e Coreutico - Sez. Coreutica', ''),
  (11, 7, 'Amm. Finan. Marketing - Biennio Comune', ''),
  (12, 7, 'Turismo', ''),
  (13, 7, 'Biennio Settore Economico', ''),
  (14, 8, 'Mecc. Meccatron. Ener. - Biennio Comune', ''),
  (15, 8, 'Biennio Settore Tecnologico', ''),
  (16, 8, 'Trasporti E Logistica - Biennio Comune', ''),
  (17, 8, 'Elettr. Ed Elettrotec.- Biennio Comune', ''),
  (18, 8, 'Infor. Telecom. - Biennio Comune', ''),
  (19, 8, 'Grafica E Comunicazione', ''),
  (20, 8, 'Chim. Mater. Biotecn. - Biennio Comune', ''),
  (21, 8, 'Sistema Moda - Biennio Comune', ''),
  (22, 8, 'Agraria, Agroal. E Agroind. - Biennio Com.', ''),
  (23, 8, 'Costr., Amb. E Territorio - Biennio Com.', ''),
  (24, 9, 'Servizi Per L&#39;Agricoltura E Lo Sviluppo Rurale', ''),
  (25, 9, 'Servizi Socio-Sanitari', ''),
  (26, 9, 'Enogas. Ospit. Alberg. - Biennio Comune', ''),
  (27, 9, 'Servizi Commerciali', ''),
  (28, 10, 'Manutenzione E Assistenza Tecnica', ''),
  (29, 10, 'Prod. Industr. Artig. - Biennio Comune', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_gopercorso`
--

CREATE TABLE IF NOT EXISTS `tbl_gopercorso` (
  `idpercorso` int(11) NOT NULL,
  `denominazione` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `tbl_gopercorso`
--

INSERT INTO `tbl_gopercorso` (`idpercorso`, `denominazione`) VALUES
  (1, 'Liceo'),
  (2, 'Istituto Tecnico'),
  (3, 'Istituto Professionale'),
  (4, 'Liceo Europeo/Internazionale'),
  (5, 'IeFP presso i centri di formazione professionale regionali'),
  (6, 'IeFP - Sussidiarietà integrativa (diploma quinquennale rilasciato dall&#39;istituto professionale + qualifica IeFP)'),
  (7, 'IeFP - Sussidiarietà complementare - percorso triennale (solo qualifica IeFP)'),
  (8, 'IeFP - Sussidiarietà complementare - percorso quadriennale (diploma IeFP)'),
  (9, 'Qualunque scelta'),
  (10, 'Apprendistato'),
  (11, 'Nessuna scelta comunicata');

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_gosettore`
--

CREATE TABLE IF NOT EXISTS `tbl_gosettore` (
  `idsettore` int(11) NOT NULL,
  `idpercorso` int(11) NOT NULL,
  `denominazione` text NOT NULL,
  `codsidi` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `tbl_gosettore`
--

INSERT INTO `tbl_gosettore` (`idsettore`, `idpercorso`, `denominazione`, `codsidi`) VALUES
  (1, 1, 'Classico', ''),
  (2, 1, 'Scientifico', ''),
  (3, 1, 'Linguistico', ''),
  (4, 1, 'Artistico', ''),
  (5, 1, 'Scienze Umane', ''),
  (6, 1, 'Musicale e Coreutico', ''),
  (7, 2, 'Istituto tecnico settore economico', ''),
  (8, 2, 'Istituto tecnico settore tecnologico', ''),
  (9, 3, 'Istituto professionale settore servizi', ''),
  (10, 3, 'Istituto professionale settore industria e artigianato', ''),
  (11, 4, 'Europeo', ''),
  (12, 4, 'Internazionale', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_goindirizzo`
--
ALTER TABLE `tbl_goindirizzo`
ADD PRIMARY KEY (`idindirizzo`);

--
-- Indexes for table `tbl_gopercorso`
--
ALTER TABLE `tbl_gopercorso`
ADD PRIMARY KEY (`idpercorso`);

--
-- Indexes for table `tbl_gosettore`
--
ALTER TABLE `tbl_gosettore`
ADD PRIMARY KEY (`idsettore`);



ALTER TABLE tbl_lezionicert
ADD idlezionenorm int(11) NOT NULL;


update tbl_lezionicert lc set idlezionenorm=
       (select idlezione from tbl_lezioni lz
         where datalezione=lc.datalezione
              and orainizio=lc.orainizio
              and numeroore=lc.numeroore
              and idmateria=lc.idmateria
              and idclasse=lc.idclasse
              and exists(select * from tbl_firme where iddocente=lc.iddocente and idlezione=lz.idlezione ) );



-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2015.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
