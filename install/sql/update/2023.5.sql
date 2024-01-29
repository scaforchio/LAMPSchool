INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'smtphost', '', 'Indirizzo server SMTP', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'smtpport', '', 'Porta server SMTP', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'smtpuser', '', 'Username server SMTP', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'smtppass', '', 'Password server SMTP', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'smtpcrypt', '', 'Crittografia server SMTP', 'none|ssl|tls');

ALTER TABLE `tbl_alunni`
  ADD `liberatoria` TINYINT NOT NULL DEFAULT 0 AFTER `totpgiustass`;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023.5' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE