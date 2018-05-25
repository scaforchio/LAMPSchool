--
-- Aggiornamento di LAMPSchool alla versione 2017.2
--

ALTER TABLE tbl_esesiti CHANGE mediascramm mediascrcolloq DECIMAL(4,2) NULL DEFAULT NULL;
ALTER TABLE tbl_esesiti
    CHANGE datacolloquio datacolloquio DATE NULL DEFAULT NULL;

INSERT INTO tbl_parametri (idparametro, gruppo, parametro, valore, descrizione, valoriammessi) VALUES (NULL, 'voti', 'solovotiinteri', 'no', 'Stabilisce se le valutazioni delle verifiche devono essere solo intere.', 'no|yes');


CREATE TABLE tbl_certcompcompetenze (
  idccc int(11) NOT NULL,
  numprogressivo int(11) DEFAULT NULL,
  compcheuropea varchar(512) DEFAULT NULL,
  compprofilo text,
  livscuola int(11) DEFAULT NULL,
  valido tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tbl_certcomplivelli (
  idccl int(11) NOT NULL,
  livello varchar(512) DEFAULT NULL,
  indicatoreesplicativo varchar(512) DEFAULT NULL,
  indicatorenumerico int(11) DEFAULT NULL,
  livscuola int(11) DEFAULT NULL,
  valido tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tbl_certcompvalutazioni (
  idccv int(11) NOT NULL,
  idalunno int(11) DEFAULT NULL,
  idccc int(11) DEFAULT NULL,
  idccl int(11) DEFAULT NULL,
  giud varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tbl_certcompproposte (
  idccp int(11) NOT NULL,
  idalunno int(11) DEFAULT NULL,
  iddocente int(11) DEFAULT NULL, 
  idccc int(11) DEFAULT NULL,
  idccl int(11) DEFAULT NULL,
  giud varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE tbl_certcomplivelli
  ADD PRIMARY KEY (idccl);

ALTER TABLE tbl_certcompproposte
  ADD PRIMARY KEY (idccp);

ALTER TABLE tbl_certcompvalutazioni
  ADD PRIMARY KEY (idccv);

ALTER TABLE tbl_certcompcompetenze
  ADD PRIMARY KEY (idccc);

ALTER TABLE tbl_certcompcompetenze
  MODIFY idccc int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE tbl_certcomplivelli
  MODIFY idccl int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE tbl_certcompvalutazioni
  MODIFY idccv int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE tbl_certcompproposte
  MODIFY idccp int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO tbl_certcompcompetenze (idccc, numprogressivo, compcheuropea, compprofilo, livscuola, valido) VALUES
(1, 1, 'Comunicazione nella madrelingua o lingua di istruzione', 'Ha una padronanza della lingua italiana che gli consente di comprendere e produrre enunciati e testi di una certa complessitÃ , di esprimere le proprie idee, di adottare un registro linguistico appropriato alle diverse situazioni.', 2, 1),
(2, 2, 'Comunicazione nella lingua straniera', 'Ãˆ in grado di esprimersi in lingua inglese a livello elementare (A2 del Quadro Comune Europeo di Riferimento) e, in una seconda lingua europea, di affrontare una comunicazione essenziale in semplici situazioni di vita quotidiana. Utilizza la lingua inglese anche con le tecnologie dellâ€™informazione e della comunicazione.', 2, 1),
(3, 3, 'Competenza matematica e competenze di base in scienza e tecnologia', 'Utilizza le sue conoscenze matematiche e scientifico-tecnologiche per analizzare dati e fatti della realtÃ  e per verificare lâ€™attendibilitÃ  di analisi quantitative proposte da altri. Utilizza il pensiero logico-scientifico per affrontare problemi e situazioni sulla base di elementi certi. Ha consapevolezza dei limiti delle affermazioni che riguardano questioni complesse.', 2, 1),
(4, 4, 'Competenze digitali', 'Utilizza con consapevolezza e responsabilitÃ  le tecnologie per ricercare, produrre ed elaborare dati e informazioni, per interagire con altre persone, come supporto alla creativitÃ  e alla soluzione di problemi.', 2, 1),
(5, 5, 'Imparare ad imparare', 'Possiede un patrimonio organico di conoscenze e nozioni di base ed Ã¨ allo stesso tempo capace di ricercare e di organizzare nuove informazioni. Si impegna in nuovi apprendimenti in modo autonomo.', 2, 1),
(6, 6, 'Competenze sociali e civiche', 'Ha cura e rispetto di sÃ¨ e degli altri come presupposto di uno stile di vita sano e corretto. Eâ€™ consapevole della necessitÃ  del rispetto di una convivenza civile, pacifica e solidale. Si impegna per portare a compimento il lavoro iniziato, da solo o insieme ad altri.', 2, 1),
(7, 7, 'Spirito di iniziativa', 'Ha spirito di iniziativa ed Ã¨ capace di produrre idee e progetti creativi. Si assume le proprie responsabilitÃ , chiede aiuto quando si trova in difficoltÃ  e sa fornire aiuto a chi lo chiede. Ãˆ disposto ad analizzare se stesso e a misurarsi con le novitÃ  e gli imprevisti.', 2, 1),
(8, 8, 'Consapevolezza ed espressione culturale', 'Riconosce ed apprezza le diverse identitÃ , le tradizioni culturali e religiose, in unâ€™ottica di dialogo e di rispetto reciproco.', 2, 1),
(9, 8, 'Consapevolezza ed espressione culturale', 'Si orienta nello spazio e nel tempo e interpreta i sistemi simbolici e culturali della societÃ ', 2, 1),
(10, 8, 'Consapevolezza ed espressione culturale', 'In relazione alle proprie potenzialitÃ  e al proprio talento si esprime negli ambiti che gli sono piÃ¹ congeniali: motori, artistici e musicali.', 2, 1),
(11, 1, 'Comunicazione nella madrelingua o lingua di istruzione', 'Ha una padronanza della lingua italiana che gli consente di comprendere enunciati, di raccontare le proprie esperienze e di adottare un registro linguistico appropriato alle diverse situazioni.', 1, 1),
(12, 2, 'Comunicazione nelle lingue straniere', 'Ãˆ in grado di sostenere in lingua inglese una comunicazione essenziale in semplici situazioni di vita quotidiana.', 1, 1),
(13, 3, 'Competenza matematica e competenze di base in scienza e tecnologia', 'Utilizza le sue conoscenze matematiche e scientifico-tecnologiche per trovare e giustificare soluzioni a problemi reali.', 1, 1),
(14, 4, 'Competenze digitali', 'Usa con responsabilitÃ  le tecnologie in contesti comunicativi concreti per ricercare informazioni e per interagire con altre persone, come supporto alla creativitÃ  e alla soluzione di problemi semplici.', 1, 1),
(15, 5, 'Imparare ad imparare', 'Possiede un patrimonio di conoscenze e nozioni di base ed Ã¨ in grado di ricercare nuove informazioni. Si impegna in nuovi apprendimenti anche in modo autonomo.', 1, 1),
(16, 6, 'Competenze sociali e civiche', 'Ha cura e rispetto di sÃ¨, degli altri e dellâ€™ambiente. Rispetta le regole condivise e collabora con gli altri. Si impegna per portare a compimento il lavoro iniziato, da solo o insieme agli altri.', 1, 1),
(17, 7, 'Spirito di iniziativa *', 'Dimostra originalitÃ  e spirito di iniziativa. Ãˆ in grado di realizzare semplici progetti. Si assume le proprie responsabilitÃ , chiede aiuto quando si trova in difficoltÃ  e sa fornire aiuto a chi lo chiede.', 1, 1),
(18, 8, 'Consapevolezza ed espressione culturale', 'Si orienta nello spazio e nel tempo, osservando e descrivendo ambienti, fatti, fenomeni e produzioni artistiche.', 1, 1),
(19, 8, 'Consapevolezza ed espressione culturale', 'Riconosce le diverse identitÃ , le tradizioni culturali e religiose in unâ€™ottica di dialogo e di rispetto reciproco.', 1, 1),
(20, 8, 'Consapevolezza ed espressione culturale', 'In relazione alle proprie potenzialitÃ  e al proprio talento si esprime negli ambiti che gli sono piÃ¹ congeniali: motori, artistici e musicali.', 1, 1),
(21, 9, '', 'Lâ€™alunno/a ha inoltre mostrato significative competenze nello svolgimento di attivitÃ  scolastiche e/o extrascolastiche, relativamente a:', 1, 1),
(22, 9, '', 'Lâ€™alunno/a ha inoltre mostrato significative competenze nello svolgimento di attivitÃ  scolastiche e/o extrascolastiche, relativamente a:', 2, 1);

INSERT INTO tbl_certcomplivelli (idccl, livello, indicatoreesplicativo, indicatorenumerico, livscuola, valido) VALUES
(1, 'A - Avanzato', 'Lâ€™alunno/a svolge compiti e risolve problemi complessi, mostrando padronanza nellâ€™uso delle conoscenze e delle abilitÃ ; propone e sostiene le proprie opinioni e assume in modo responsabile decisioni consapevoli.', 4, 1, 1),
(2, 'B - Intermedio', 'Lâ€™alunno/a svolge compiti e risolve problemi in situazioni nuove, compie scelte consapevoli, mostrando di saper utilizzare le conoscenze e le abilitÃ  acquisite.', 3, 1, 1),
(3, 'C - Base', 'Lâ€™alunno/a svolge compiti semplici anche in situazioni nuove, mostrando di possedere conoscenze e abilitÃ  fondamentali e di saper applicare basilari regole e procedure apprese.', 2, 1, 1),
(4, 'D - Iniziale', 'Lâ€™alunno/a, se opportunamente guidato/a, svolge compiti semplici in situazioni note.', 1, 1, 1),
(5, 'A - Avanzato', 'Lâ€™alunno/a svolge compiti e risolve problemi complessi, mostrando padronanza nellâ€™uso delle conoscenze e delle abilitÃ ; propone e sostiene le proprie opinioni e assume in modo responsabile decisioni consapevoli.', 4, 2, 1),
(6, 'B - Intermedio', 'Lâ€™alunno/a svolge compiti e risolve problemi in situazioni nuove, compie scelte consapevoli, mostrando di saper utilizzare le conoscenze e le abilitÃ  acquisite.', 3, 2, 1),
(7, 'C - Base', 'Lâ€™alunno/a svolge compiti semplici anche in situazioni nuove, mostrando di possedere conoscenze e abilitÃ  fondamentali e di saper applicare basilari regole e procedure apprese.', 2, 2, 1),
(8, 'D - Iniziale', 'Lâ€™alunno/a, se opportunamente guidato/a, svolge compiti semplici in situazioni note.', 1, 2, 1);

ALTER TABLE tbl_esesiti
    CHANGE datacolloquio datacolloquio DATE NULL DEFAULT NULL;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'timbratureritardiabilitati', 'yes', 'Abilitazione a ricezione timbrature ritardi', 'no|yes');

ALTER TABLE tbl_alunni ADD `oraultimamodifica` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2017.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
