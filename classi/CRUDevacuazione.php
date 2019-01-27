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
$daticrud['titolo'] = 'GESTIONE CLASSI';


$daticrud['tabella'] = ("tbl_classi");


// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "classi";
$daticrud['larghezzatabella'] = "100%";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idclasse";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
$daticrud['campiordinamento'] = "anno,specializzazione,sezione";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = ("true");
if ($_SESSION['tipoutente'] == 'D')
{
    $iddocente = $_SESSION['idutente'];
    $daticrud['condizione'] = ("idcoordinatore=$iddocente");
}
$daticrud['abilitazionemodifica'] = 1;
$daticrud['abilitazionecancellazione'] = 0;
$daticrud['abilitazioneinserimento'] = 0;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1, ''];
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
 */

//if ($_SESSION['tipoutente'] == 'D')
//    $selezioneclasse = "select idclasse from tbl_classi where idcoordinatore=$iddocente";

$daticrud['campi'] = [
    ['idclasse', '1', ('tbl_classi'), 'idclasse', 'anno,sezione,specializzazione', 0, 'Classe', 1, '', '', 1, '', '', 1,'',1],
    ['aprifila1', 2, ('tbl_alunni'), 'idalunno', 'cognome,nome', 0, 'Aprifila 1', 1, '', '', 0, '', '', 0, 'cognalu1,nomealu1', 0, ('select distinct(idalunno) from tbl_alunni where idclasse=')],
    ['aprifila2', 3, ('tbl_alunni'), 'idalunno', 'cognome,nome', 0, 'Aprifila 2', 2, '', '', 0, '', '', 0, 'cognalu2,nomealu2', 0, ('select distinct(idalunno) from tbl_alunni where idclasse=')],
    ['chiudifila1', 4, ('tbl_alunni'), 'idalunno', 'cognome,nome', 0, 'Chiudifila 1', 3, '', '', 0, '', '', 0, 'cognalu3,nomealu3', 0, ('select distinct(idalunno) from tbl_alunni where idclasse=')],
    ['chiudifila2', 5, ('tbl_alunni'), 'idalunno', 'cognome,nome', 0, 'Chiudifila 2', 4, '', '', 0, '', '', 0, 'cognalu4,nomealu4', 0, ('select distinct(idalunno) from tbl_alunni where idclasse=')]
];


$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

