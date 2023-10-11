ALTER TABLE `tbl_alunni` CHANGE `censito` `censito` VARCHAR(5) NOT NULL DEFAULT '0';
-- FORMATO 0 è disattivato, ABCD ogni lettera per autorizzazione N nessuno M madre P padre E entrambi

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023.3' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE