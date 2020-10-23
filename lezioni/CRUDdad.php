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
$daticrud['titolo'] = 'GESTIONE GIORNATE D.A.D.';


$daticrud['tabella'] = ("tbl_dad");

$daticrud['larghezzatabella'] = "80%";
// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "dad";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "iddad";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
// Aggiungere DESC in caso di ordinamento inverso
$daticrud['campiordinamento'] = "anno,specializzazione,sezione";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = "true";

$daticrud['abilitazionemodifica'] = 0;
$daticrud['abilitazionecancellazione'] = 1;
$daticrud['abilitazioneinserimento'] = 0;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [0, ''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
/* $daticrud['vincolicanc'] = [
    ['tbl_alunni', 'idclasse'],
    ['tbl_competalu', 'idclasse'],
    ['tbl_competdoc', 'idclasse'],
    ['tbl_documenti', 'idclasse'],
    ['tbl_entrateclassi', 'idclasse'],
    ['tbl_esiti', 'idclasse'],
    ['tbl_esami3m', 'idclasse'],
    ['tbl_esmaterie', 'idclasse'],
    ['tbl_giudizi', 'idclasse'],
    ['tbl_lezioni', 'idclasse'],
    ['tbl_lezionicert', 'idclasse'],
    ['tbl_notealunno', 'idclasse'],
    ['tbl_noteclasse', 'idclasse'],
    ['tbl_osssist', 'idclasse'],
    ['tbl_scrutini', 'idclasse'],
    ['tbl_valutazionicomp', 'idclasse'],
    ['tbl_valutazioniintermedie', 'idclasse'],
    ['tbl_annotazioni', 'idclasse'],
    ['tbl_assemblee', 'idclasse'],
    ['tbl_cambiamenticlasse', 'idclasse'],
    ['tbl_cattnosupp', 'idclasse'],
    ['tbl_cattsupp', 'idclasse']
];
 * 
 */


/*
  // Significato valori
 * 0 - campo -nome campo tabella principale
 * 1 - ordtabella - ordine di visualizzazione in tabella (0 non visualizzata)
 * 2 - tabesterna - tabella esterna
 * 3 - chiavetabesterna - campo chiave nella tabella esterna
 * 4 - campivistabesterna - campo o campi (separati da virgola) dei dati da visualizzare
 * 5 - dimmaxinput - dimensione massima del campo per input (0 per chiavi esterne)
 * 6 - labelcampo - Label per indicazione campo
 * 7 - ordinput - Ordine di visualizzazione in maschere di inserimento o modifica (0 non presenti)
 * 8 - tipocampo - Tipo campo per inserimento o modifica (text, date, time, number, boolean, testo)
 * 9 - spiegazione - Spiegazione del contenuto del campo (visualizzato in piccolo sotto alla Label)
 * 10 - obbligatorio - obbligatorio (0 - no, 1 -sì)
 * 11 - valmin - valore minimo ('' per non usarlo)
 * 12 - valmax - valore massimo ('' per non usarlo)
 * 13 - possselfiltro - possibilità di selezione per filtro
 * 14 - elcampialias - elenco eventuali alias dei campi se chiave esterna da visualizzare se usato alias
 * 15 - disabmodifica - modifica disabilitata
 * 16 - selesternadaprimaria - condizione selezione dei valori della tabella esterna, select che restituisce un elenco di valori chiave primaria 
 *      della tabella esterna con riferimento alla chiave primaria della tabella principale
 * 17 - selesternadaaltro - condizione di selezione dei valori della tabella esterna basata su un valore ricavabile
 *      in fase di preparazione dei daticrud (Es. iddocente, tipoutente, ecc.)
 * 18 - clausdistinct - clausola distinct nella selezione dei valori della tabella esterna (1-sì, 0-no) 
 */

$daticrud['campi'] = [
    ['0'=>'idclasse','1'=> '1', '2'=>'tbl_classi', '3'=>'idclasse','4'=> 'anno,sezione,specializzazione', '5'=>0, '6'=>'Classe', '7'=>4, '15'=>1],
    ['0'=>'datadad', '1'=>'2', '6'=>'Data D.A.D.'],
     ];



$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

