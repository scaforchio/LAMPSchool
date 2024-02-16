CREATE TABLE tbl_sondaggi (
    `idsondaggio` INT NOT NULL AUTO_INCREMENT , 
    `oggetto` VARCHAR(255) NOT NULL , 
    `descrizione` TEXT NOT NULL , 
    `opzioni` JSON NOT NULL,
    `attivo` TINYINT NOT NULL DEFAULT '0',
    PRIMARY KEY (`idsondaggio`)
) ENGINE = InnoDB;

CREATE TABLE tbl_rispostesondaggi (
    `idrisposta` INT NOT NULL AUTO_INCREMENT , 
    `idutente` INT NOT NULL , 
    `idsondaggio` INT NOT NULL , 
    `opzione` INT NOT NULL , 
    PRIMARY KEY (`idrisposta`)
) ENGINE = InnoDB;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023.6' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE