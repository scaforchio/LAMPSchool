--
-- Aggiornamento di LAMPSchool alla versione 2020.6
--

CREATE TABLE IF NOT EXISTS tbl_otp (
  idotp int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  valore char(32) DEFAULT '',
  idutente int(11),
  funzione char(32) DEFAULT '',
  nummaxutilizzi int(2) DEFAULT 0,
  numutilizzi int(2) DEFAULT 0,
  timecreazione int(11) DEFAULT 0,
  timeultimoutilizzo int(11) DEFAULT 0,
  valido boolean DEFAULT 1)
  ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_testisms
MODIFY idinvio char(100);



-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.6' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
