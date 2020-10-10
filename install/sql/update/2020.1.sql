--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--

CREATE TABLE IF NOT EXISTS tbl_seed (
  idseed int(11) AUTO_INCREMENT PRIMARY KEY,
  seed char(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
