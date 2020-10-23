--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--

CREATE TABLE IF NOT EXISTS tbl_seed (
  idseed int(11) AUTO_INCREMENT PRIMARY KEY,
  seed char(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'tipogestassenzelezione', 'auto', 'man - inserimento manuale , auto - calcolo da ritardi e uscite, ibr - ibrido','auto|man|ibr');

CREATE TABLE IF NOT EXISTS tbl_dad (
  iddad int(11) AUTO_INCREMENT PRIMARY KEY,
  idclasse int(11),
  datadad date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_alunni
ADD email2 varchar(100) DEFAULT NULL;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
