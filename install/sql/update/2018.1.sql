--
-- Aggiornamento di LAMPSchool alla versione 2018
--

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'sitoinmanutenzione', 'no', 'Blocco accesso per genitori e docenti non staff', 'yes|no');

ALTER TABLE tbl_richiesteferie ADD numerogiorni TINYINT NOT NULL DEFAULT 0 AFTER oraultmod, 
                               ADD orepermessobreve TINYINT NOT NULL DEFAULT 0 AFTER numerogiorni;

CREATE TABLE IF NOT EXISTS `tbl_autorizzazioniuscite` (
  `idalunno` int(11) DEFAULT '0',
  `data` date DEFAULT NULL,
  `orauscita` time DEFAULT NULL,
  `idautorizzazioneuscita` int(11),
  `iddocenteautorizzante` int(11) DEFAULT NULL,
  `testoautorizzazione` varchar(500) DEFAULT NULL,
  `oraultmod` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tbl_autorizzazioniuscite`
ADD PRIMARY KEY (`idautorizzazioneuscita`);
ALTER TABLE `tbl_autorizzazioniuscite`
MODIFY `idautorizzazioneuscita` int(11) AUTO_INCREMENT;

ALTER TABLE tbl_annotazioni
ADD visibilitagenitori boolean;

ALTER TABLE tbl_richiesteferie CHANGE testomail testomail 
VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci 
NULL DEFAULT NULL;


--
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2018.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
