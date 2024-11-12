ALTER TABLE `tbl_rispostesondaggi` ADD `ts` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `idopzione`;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2024.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE