ALTER TABLE tbl_utenti ADD totpsecret VARCHAR(255) NOT NULL DEFAULT 'disabled' AFTER numutilizzitoken;

-- LASCIARE SEMPRE ALLA FINE
UPDATE tbl_parametri set valore='2026.1' where parametro='versioneprecedente';
-- LASCIARE SEMPRE ALLA FINE