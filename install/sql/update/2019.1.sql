--
-- Aggiornamento di LAMPSchool alla versione 2019.1
--


-- Ampliamento campi autorizzazione entrata e uscita

ALTER TABLE tbl_alunni CHANGE autentrata autentrata VARCHAR(100) NULL DEFAULT NULL, 
                       CHANGE autuscita autuscita VARCHAR(100) NULL DEFAULT NULL;

-- Parametrizzazione email per segnalazione anomalie timbrature

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'emailgestbadge', '', 'Email del gestore dei badge per le timbrature','');


-- Modifica per controllo accessi da TOR

CREATE TABLE IF NOT EXISTS tbl_torlist (
  idtorlist int(11) AUTO_INCREMENT PRIMARY KEY,
  indirizzo char(60),
  oraultmod timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tbl_torlist
ADD KEY indirizzo (indirizzo);


-- Modifica per Token

ALTER TABLE tbl_utenti
ADD token char(5);

ALTER TABLE tbl_utenti
ADD schematoken char(50);

ALTER TABLE tbl_utenti
ADD modoinviotoken char(1);

INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'tokenbototp', '', 'Token del BOT Telegram di ricezione OTP per accesso','');
INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'urlbottelegram', 'https://www.sitoscuola.it/lampschool/bots/', 'URL del bot telegram','');

ALTER TABLE tbl_utenti
ADD idtelegram int(15);

CREATE TABLE IF NOT EXISTS tbl_confermatelegram (
  idutente int(11),
  tokendiconferma char(200),
  oraultmod timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE tbl_sospensionicolloqui
ADD datafine date;

update tbl_sospensionicolloqui set datafine=data where 1=1;


INSERT INTO tbl_parametri (gruppo, parametro, valore, descrizione, valoriammessi) VALUES ('sistema', 'gensolocomunicazioni', 'no', 'Abilita il registro ai genitori solo per comunicazioni','no|yes');


-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2019.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE
