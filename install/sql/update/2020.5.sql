--
-- Aggiornamento di LAMPSchool alla versione 2020.5
--
ALTER TABLE tbl_parametri ADD UNIQUE (parametro);

ALTER TABLE tbl_utenti
ADD tokenresetpwd  char(32);
ALTER TABLE tbl_utenti
ADD  oracreazionetoken timestamp;
ALTER TABLE tbl_utenti
ADD  numutilizzitoken tinyint(1);
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2020.5' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
