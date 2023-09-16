INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'gest_itrp', 'no','Rilevazione entrate e uscite tramite NUOVO sistema di badge. NON ABILITARE CON IL VECCHIO SISTEMA ATTIVO!', 'yes|no');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'protogiustonline', 'totp', 'Protocollo di gestione OTP assenze online', 'totp|sms');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('tempo', 'orarioingresso', '08:05', 'Orario massimo di ingresso a scuola dopo il quale viene registrato un ritardo espresso in formato HH:mm', '');

ALTER TABLE `tbl_alunni`
  ADD `censito` TINYINT NOT NULL DEFAULT '0' after `autorizzazioni`,
  ADD `idgrupporitardo` INT NOT NULL DEFAULT '1' after `censito`;

ALTER TABLE `tbl_alunni` ADD `totpgiustass` VARCHAR(120) NULL DEFAULT NULL AFTER `autorizzazioni`;

CREATE TABLE IF NOT EXISTS tbl_gruppiritardi ( 
    `idgrupporitardo` INT NOT NULL AUTO_INCREMENT , 
    `minutiaggiuntivi` INT NOT NULL , 
    `descrizione` VARCHAR(50) NOT NULL , 
    PRIMARY KEY (`idgrupporitardo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tbl_gruppiritardi (idgrupporitardo, minutiaggiuntivi, descrizione) VALUES (1, 0, "Gruppo Predefinito");

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE