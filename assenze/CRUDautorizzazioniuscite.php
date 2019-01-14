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
$daticrud['titolo'] = 'GESTIONE AUTORIZZAZIONI USCITE';


$daticrud['tabella'] = "tbl_autorizzazioniuscite";


// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "Autorizzazioni uscite";
$daticrud['larghezzatabella'] = "80%";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idautorizzazioneuscita";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
$daticrud['campiordinamento'] = "cognalu,nomealu,data";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = "true"; // Campi in base ai quali ordinare

$daticrud['abilitazionemodifica'] = 1;
$daticrud['abilitazionecancellazione'] = 1;
$daticrud['abilitazioneinserimento'] = 0;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1, ''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
/* $daticrud['vincolicanc'] = [

  [inspref('tbl_noteindalu'),'idalunno'],
  [inspref('tbl_assenze'),'idlaunno']

  ];

 */
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
 */


$daticrud['campi'] = [
    ['idalunno', '1', 'tbl_alunni', 'idalunno', 'cognome,nome', 0, 'Alunno', 1, '', '', 1, '', '', 1, 'cognalu,nomealu', 1],
    ['data', '2', '', '', '', 10, 'Data', 2, 'date', '', 1, '', '', 1, '', 1],
    ['orauscita', '3', '', '', '', 10, 'Ora uscita', 3, 'time', '', 1, '', '', 1, '', 1],
    ['iddocenteautorizzante', '4', 'tbl_docenti', 'iddocente', 'cognome,nome', 0, 'Docente', 4, '', '', 1, '', '', 1, '', 1],
    ['testoautorizzazione', '5', '', '', '', 100, 'Autorizzazione', 5, 'text', '', 1, '', '', 0, '', 0],
];


$_SESSION['daticrud'] = $daticrud;

header("location: ../lib/CRUD.php?suffisso=" . $_SESSION['suffisso']);

//session_start();
///*
//  Copyright (C) 2018 Pietro Tamburrano
//  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
//  GNU Affero General Public License come pubblicata
//  dalla Free Software Foundation; sia la versione 3,
//  sia (a vostra scelta) ogni versione successiva.
//
//  Questo programma è distribuito nella speranza che sia utile
//  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
//  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
//  Vedere la GNU Affero General Public License per ulteriori dettagli.
//
//  Dovreste aver ricevuto una copia della GNU Affero General Public License
//  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
// */
//
//
//require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
//require_once '../lib/funzioni.php';
//
//
//
//$daticrud = array();
//// Tabella da modificare
//$daticrud['tabella'] = inspref("tbl_annotazioni");
//$daticrud['titolo']='GESTIONE ANNOTAZIONI';
//// Nome della tabella per visualizzazioni
//$daticrud['aliastabella'] = "Annotazioni su registro";
//// Campo con l'id univoco per la tabella
//$daticrud['campochiave'] = "idannotazione";
//
//// Campi in base ai quali ordinare
//$daticrud['campiordinamento']= array("anno,sezione,specializzazione,data");
//// Condizione di selezione, specificare solo 'true' se non ce ne sono
//$daticrud['condizione']= inspref("true");// Campi in base ai quali ordinare
//
//$daticrud['abilitazionemodifica']=0;
//$daticrud['abilitazionecancellazione']=1;
//$daticrud['abilitazioneinserimento']=1;
//
//// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)
//
//$daticrud['confermacancellazione'] = [1,''];
//// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
//// specificato
///*$daticrud['vincolicanc'] = [
//                            
//                            [inspref('tbl_noteindalu'),'idalunno'],
//                            [inspref('tbl_assenze'),'idlaunno']
//                            
//                           ];
//
//*/
///*
//// Significato valori
// * 0 - nome campo tabella principale
// * 1 - ordine di visualizzazione in tabella (0 non visualizzata)
// * 2 - tabella esterna
// * 3 - campo chiave nella tabella esterna
// * 4 - campo o campi (separati da virgola) dei dati da visualizzare
// * 5 - dimensione massima del campo per input (0 per chiavi esterne)
// * 6 - Label per indicazione campo
// * 7 - Ordine di visualizzazione in maschere di inserimento o modifica (0 non presenti)
// * 8 - Tipo campo per inserimento o modifica (text, date, time, number, boolean)
// * 9 - spiegazione del contenuto
// * 10 - obbligatorio (0 - no, 1 -sì)
// * 11 - valore minimo ('' per non usarlo)
// * 12 - valore massimo ('' per non usarlo)
// * 13 - possibilità selezione nella lista
// */
//
//
//$daticrud['campi'] = [
//                      ['idclasse','1',inspref('tbl_classi'),'idclasse','anno,sezione,specializzazione',0,'Classe',1,'','',1,'','',1],
//                      ['iddocente','3',inspref('tbl_docenti'),'iddocente','cognome,nome',0,'Docente',2,'','',1,'','',1],
//                      ['data','2','','','',10,'Data annotazione',3,'date','',1,'','',1],
//                      ['testo','4','','','50',1,'Testo annotazione',4,'testo','',1,'1','9',1 ],
//                      ['visibilitagenitori','5','','','',50,'Visibile a tutti i genitori della classe',5,'boolean','',1,'','',0 ],
//                      ['visibilitaalunni','6','','','',50,'Visibile a tutti gli alunni della classe',6,'boolean','',1,'','',0 ]
//    
//                     ];
//
//
//
//
//
//$_SESSION['daticrud'] = $daticrud;
//
//header("location: ../lib/CRUD.php?suffisso=".$_SESSION['suffisso']);
//
