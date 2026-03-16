
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'tokenbotavvisi', '','token del bot telegram da usare per mandare avvisi di amministrazione urgenti', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'chatidavvisi', '','dove mandare avvisi di amministrazione urgenti', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'totpsecretunikey', 'disabled','secret TOTP da usare durante accessi unikey (imposta disabled per disabilitare)', '');

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2026.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE