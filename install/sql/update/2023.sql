INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'broker_host', NULL, 'Hostname del broker MQTT per il modulo Active Directory', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'broker_port', '1883', 'Porta del broker MQTT per il modulo Active Directory', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'broker_user', NULL, 'Username del broker MQTT per il modulo Active Directory', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'broker_pass', NULL, 'Password del broker MQTT per il modulo Active Directory', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'broker_topic', 'adsync', 'Topic mqtt su cui inviare le code', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'ad_module_enabled', 'no', 'Abilitazione modulo Active Directory', 'yes|no');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'adgroup_alunni', NULL, 'Gruppo AD da associare agli alunni', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'adgroup_docenti', NULL, 'Gruppo AD da associare ai docenti', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'adgroup_amministrativi', NULL, 'Gruppo AD da associare agli amministrativi', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'adgroup_presidi', NULL, 'Gruppo AD da associare ai presidi', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('adsync', 'adautosync_disabled', 'no', 'Disattivazione sincronizzazione automatica', 'yes|no');
ALTER TABLE tbl_utenti ADD wifi TINYINT NOT NULL DEFAULT '0' AFTER oidc_authmode;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2023' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE