CREATE TABLE `tbl_fotoannuario` (
    `id_foto` INT NOT NULL AUTO_INCREMENT , 
    `hash` VARCHAR(32) NOT NULL ,
    `didascalia` VARCHAR(255) NOT NULL , 
    PRIMARY KEY (`id_foto`)
);

ALTER TABLE `tbl_alunni`
  ADD `idfotoannuario` INT NOT NULL DEFAULT 0 AFTER `liberatoria`;

ALTER TABLE `tbl_classi`
  ADD `idfotoannuario` INT NOT NULL DEFAULT 0 AFTER `chiudifila2`;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'anniannuario', '5','Lista numerica separata da virgola degli anni abilitati alla funzione (ex. "4,5" per tutte le classi del quarto e quinto anno)', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'annuariopubblico', 'no','Indica se pubblicare annuario su menu studenti', 'yes|no');


-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2024' where psarametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE