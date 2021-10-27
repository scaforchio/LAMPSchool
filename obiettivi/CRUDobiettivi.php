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
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);


$daticrud = array();
// Tabella da modificare
$daticrud['titolo'] = 'GESTIONE OBIETTIVI';


$daticrud['tabella'] = ("tbl_obiettivi");

$daticrud['larghezzatabella'] = "80%";
// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "obiettivi";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idobiettivo";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
// Aggiungere DESC in caso di ordinamento inverso
$daticrud['campiordinamento'] = "idmateria,progressivo";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$iddocente = $_SESSION['idutente'];
$elencomaterie = elenco_materie_docente($con, $iddocente);
$elencoclassi = elenco_classi_docente($con, $iddocente);
//print $elencoclassi;
if ($_SESSION['tipoutente'] == 'D')
{

    $daticrud['condizione'] = " exists (select * from tbl_cattnosupp where idmateria=obiettivi.idmateria and idclasse=obiettivi.idclasse and iddocente=$iddocente)";
} else
    $daticrud['condizione'] = "true"; // Campi in base ai quali ordinare


$daticrud['abilitazionemodifica'] = 1;
$daticrud['abilitazionecancellazione'] = 1;
$daticrud['abilitazioneinserimento'] = 1;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1, ''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
$daticrud['vincolicanc'] = [
    ['tbl_valutazioniobiettivi', 'idobiettivo'],
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
 * 17 - selesternadaaltro - condizione di selezione dei valori della tabella esterna basata su un elenco di valorivalore ricavabile
 *      in fase di preparazione dei daticrud (Es. iddocente, tipoutente, ecc.)
 * 18 - clausdistinct - clausola distinct nella selezione dei valori della tabella esterna (1-sì, 0-no) 
 */

$daticrud['campi'] = [
 ['0' => 'idclasse', '1' => '1', '2' => 'tbl_classi', '3' => 'idclasse', '4' => 'anno,sezione,specializzazione', '6' => 'Classe', '7' => 1, '8' => 'text', '15' => 1,'17'=>"$elencoclassi"],
 ['0' => 'idmateria', '1' => '2', '2' => 'tbl_materie', '3' => 'idmateria', '4' => 'denominazione', '6' => 'Materia', '7' => 2, '8' => 'text', '15' => 1,'17'=>"$elencomaterie"],
 ['0' => 'progressivo', '1' => '3', '10' => 15,'6'=> 'Progressivo','7'=> 3,'8'=> 'number','10'=>1,'11'=>1,'12'=>10],
 ['0'=>'obiettivo', '1'=>'4', '6'=>'Obiettivo','7'=> 4, '8'=>'testo', '10'=> 1, '11'=>'1', '13'=> 0, '15'=>0],
    
    ];



$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

