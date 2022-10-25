INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('autenticazione', 'oidc_issuer', NULL, 'URL del server e realm di autenticazione OpenID', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('autenticazione', 'oidc_client_id', NULL, 'Client ID', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('autenticazione', 'oidc_enabled', 'no', 'Abilita accesso tramite OIDC, se impostato su exclusive l accesso al registro sarà possibile solo tramite OIDC.', 'no|yes|exclusive');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('autenticazione', 'oidc_provider_name', NULL, 'Nome da mostrare sulla pagina di accesso per OIDC.', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('autenticazione', 'oidc_client_secret', NULL, 'Client secret', '');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('autenticazione', 'oidc_redirect_uri', NULL, 'Redirect URI dopo il logout', '');


ALTER TABLE tbl_utenti ADD oidc_uid VARCHAR(255) NOT NULL  AFTER numutilizzitoken, ADD oidc_authmode VARCHAR(1) NOT NULL DEFAULT 'd' COMMENT 'd = disabled, e = enabled, x = exclusive'  AFTER oidc_uid;

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('scuola', 'abilgiustonline', 'no', 'Abilitazione giustifiche online (yes/no).', 'yes|no');
-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2022.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE