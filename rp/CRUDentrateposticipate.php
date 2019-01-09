<?php

session_start();
/*
  Copyright (C) 2018 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */


require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';



$daticrud = array();
// Tabella da modificare
$daticrud['titolo'] = 'GESTIONE ENTRATE POSTICIPATE CLASSI';


$daticrud['tabella'] = ("tbl_entrateclassi");

$daticrud['larghezzatabella'] = "80%";
// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "classi";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "identrataclasse";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
$daticrud['campiordinamento'] = "data";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = "true";

$daticrud['abilitazionemodifica'] = 0;
$daticrud['abilitazionecancellazione'] = 1;
$daticrud['abilitazioneinserimento'] = 1;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [0, ''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
$daticrud['vincolicanc'] = [
    
];


/*
  // Significato valori
 * 0 - nome campo tabella principale
 * 1 - ordine di visualizzazione in tabella (0 non visualizzata)
 * 2 - tabella esterna
 * 3 - campo chiave nella tabella esterna
 * 4 - campo o campi (separati da virgola) dei dati da visualizzare
 * 5 - dimensione massima del campo per input (0 per chiavi esterne)
 * 6 - Label per indicazione campo
 * 7 - Ordine di visualizzazione in maschere di inserimento o modifica (0 non presenti)
 * 8 - Tipo campo per inserimento o modifica (text, date, time, number, boolean, testo)
 * 9 - Spiegazione del contenuto del campo (visualizzato in piccolo sotto alla Label)
 * 10 - obbligatorio (0 - no, 1 -sì)
 * 11 - valore minimo ('' per non usarlo)
 * 12 - valore massimo ('' per non usarlo)
 * 13 - possibilità di selezione per filtro
 * 14 - elenco eventuali alias dei campi se chiave esterna da visualizzare se usato alias
 * 15 - modifica disabilitata
 * 16 - condizione selezione dei valori della tabella esterna, select che restituisce un elenco di valori chiave primaria 
 *      della tabella esterna con riferimento alla chiave primaria della tabella principale
 * 17 - condizione di selezione dei valori della tabella esterna basata su un valore ricavabile
 *      in fase di preparazione dei daticrud (Es. iddocente, tipoutente, ecc.)
 * 18 - clausola distinct nella selezione dei valori della tabella esterna (1-sì, 0-no)
 
 */

$daticrud['campi'] = [
    ['idclasse', 1, 'tbl_classi', 'idclasse', 'anno,sezione,specializzazione', 1, 'Classe', 1, '', '', 0, '', '', 1, '', 0],
    ['data', 2, '', '', '', 1, 'Data', 2, 'date', '', 1, '', '', 0, '', 1],
    ['ora', 3, 'tbl_orario', 'inizio', 'inizio', 1, 'Ora', 3, '', '', 1, '', '', 0, '', 1,'','',1]
    
];


$_SESSION['daticrud'] = $daticrud;

header("location: ../crudtabelle/CRUD.php?suffisso=" . $_SESSION['suffisso']);

