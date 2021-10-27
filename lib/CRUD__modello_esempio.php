<?php

require_once '../lib/req_apertura_sessione.php';
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
$daticrud['titolo'] = 'GESTIONE CLASSI';


$daticrud['tabella'] = ("tbl_classi");

$daticrud['larghezzatabella'] = "80%";
// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "classi";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idclasse";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
// Aggiungere DESC in caso di ordinamento inverso
$daticrud['campiordinamento'] = "anno DESC,specializzazione,sezione";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = "true";

$daticrud['abilitazionemodifica'] = 1;
$daticrud['abilitazionecancellazione'] = 1;
$daticrud['abilitazioneinserimento'] = 1;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1, ''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
$daticrud['vincolicanc'] = [
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
    ['0'=>'cognome','1'=>'1','5'=> 30,'6'=>'Cognome','7'=>1,'8'=>'text','15'=>1],
    ['0'=>'nome','1'=> '2', '5'=> 30,'6'=> 'Nome','7'=> 2,'8'=> 'text','15'=>1],
    ['0'=>'datanascita', '1'=>'3', '5'=>10, '6'=>'Data nascita','7'=> 3, '8'=>'text', '10'=> 1, '11'=>'1', '13'=> 0, '15'=> 1],
    ['0'=>'idclasse','1'=> '4', '2'=>'tbl_classi', '3'=>'idclasse','4'=> 'anno,sezione,specializzazione', '5'=>0, '6'=>'Classe', '7'=>4, '15'=>1],
    ['0'=>'firmapropria', '1'=>'5', '6'=>'Aut. firma propria', '7'=>5, '8'=>'boolean'],
    ['0'=>'autorizzazioni','1'=> '6','6'=> 'Autorizzazioni', '7'=>6,'8'=> 'testo'],
    ['0'=>'autentrata', '1'=>'7', '5'=> 30, '6'=> 'Autorizzazioni entrata','7'=> 7, '8'=>'text'],
    ['0'=>'autuscita', '1'=>'8', '5'=> 30, '6'=> 'Autorizzazioni uscita', '7'=>8, '8'=>'text']
    ];



$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

