INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('redis', 'redis_host', NULL, 'Hostname del server REDIS', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('redis', 'redis_port', '6379', 'Porta del server REDIS', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('redis', 'redis_user', NULL, 'Username del server REDIS', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('redis', 'redis_pass', NULL, 'Password del server REDIS', '');

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'entrate_max', '5', 'Numero di ingressi in ritardo per quadrimestre che fa scattare allarme rosso sulla pagina di inserimento', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'uscite_max', '5', 'Numero di uscite anticipate per quadrimestre che fa scattare allarme rosso sulla pagina', '');

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2022.2' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE