--
-- Aggiornamento di LAMPSchool alla versione 2020.8
--

CREATE TABLE IF NOT EXISTS tbl_slotcolloqui (
  idslotcolloqui int(11) AUTO_INCREMENT PRIMARY KEY,
  iddocente int(11) not null,
  idgiornatacolloqui int(11) not null,
  idalunno int(11) not null,
  orainizio time not null,
  orafine time not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tbl_giornatacolloqui (
  idgiornatacolloqui int(11) AUTO_INCREMENT PRIMARY KEY,
  data date not null,
  orainizio time not null,
  orafine time not null,
  durataslot tinyint not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tbl_colloquiclasse (
  idcolloquioclasse int(11) AUTO_INCREMENT PRIMARY KEY,
  idgiornatacolloqui int(11) not null,
  idclasse int(11) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS tbl_assenzedocenticolloqui (
  idassenzadocentecolloquio int(11) AUTO_INCREMENT PRIMARY KEY,
  idgiornatacolloqui int(11) not null,
  iddocente int(11) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.8' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
