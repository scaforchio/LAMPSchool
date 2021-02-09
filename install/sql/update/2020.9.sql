--
-- Aggiornamento di LAMPSchool alla versione 2020.9
--

CREATE TABLE IF NOT EXISTS tbl_obiettivi (
  idobiettivo int(11) AUTO_INCREMENT PRIMARY KEY,
  idclasse int(11) not null,
  idmateria int(11) not null,
  progressivo tinyint,
  obiettivo text not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tbl_valutazioniobiettivi (
  idvalutazioneobiettivo int(11) AUTO_INCREMENT PRIMARY KEY,
  idalunno int(11) not null,
  idobiettivo int(11) not null,
  idlivelloobiettivo int(11) not null default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tbl_livelliobiettivi (
  idlivelloobiettivo int(11) AUTO_INCREMENT PRIMARY KEY,
  descrizione char(50) not null,
  abbreviazione char(10) not null 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tbl_livelliobiettivi VALUES
  (1, 'In via di prima acquisizione', 'PRIMA ACQ'),
  (2, 'Base', 'BASE'),
  (3, 'Intermedio', 'INTERM'),
  (4, 'Avanzato', 'AVANZ');
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.9' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
