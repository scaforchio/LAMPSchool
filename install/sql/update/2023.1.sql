INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'gest_itrp', 'no','Rilevazione entrate e uscite tramite NUOVO sistema di badge. NON ABILITARE CON IL VECCHIO SISTEMA ATTIVO!', 'yes|no');

ALTER TABLE `it_tbl_alunni` ADD `maggiorenne` TINYINT NOT NULL DEFAULT '0' AFTER `autorizzazioni`, ADD `censito` TINYINT NOT NULL DEFAULT '0' AFTER `maggiorenne`, ADD `idgrupporitardo` INT NOT NULL DEFAULT '0' AFTER `censito`;

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