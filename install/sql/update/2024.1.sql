CREATE TABLE `tbl_passwordalt` (`id` INT NOT NULL AUTO_INCREMENT , `userid` VARCHAR(70) NOT NULL , `hash` VARCHAR(70) NOT NULL , `descrizione` VARCHAR(70) NOT NULL , PRIMARY KEY (`id`));

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2024.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE