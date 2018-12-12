<?php

session_start();

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';



$daticrud = array();
// Tabella da modificare
$daticrud['tabella'] = ("tbl_recuperipermessi");

// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "Recuperi Permessi";
$daticrud['larghezzatabella'] = "80%";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idrecupero";

// Campi in base ai quali ordinare
$daticrud['campiordinamento'] = "cognome,nome,datarecupero";
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione'] = ("true"); // Campi in base ai quali ordinare

$daticrud['abilitazionemodifica'] = 1;
$daticrud['abilitazionecancellazione'] = 1;
$daticrud['abilitazioneinserimento'] = 1;

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
 * 8 - Tipo campo per inserimento o modifica (text, phone, date, time, number)
 * 9 - spiegazione del contenuto
 * 10 - obbligatorio (0 - no, 1 -sì)
 * 11 - valore minimo ('' per non usarlo)
 * 12 - valore massimo ('' per non usarlo)
 */

$daticrud['titolo'] = 'GESTIONE RECUPERI';
$daticrud['campi'] = [
    ['iddocente', '1', ('tbl_docenti'), 'iddocente', 'cognome,nome', 0, 'Docente', 1, '', '', 1, '', ''],
    ['datarecupero', '2', '', '', '', 10, 'Data recupero', 2, 'date', '', 1, '', ''],
    ['numeroore', '3', '', '', '', 1, 'Numero ore', 3, 'number', '', 1, '1', '9'],
    ['motivo', '4', '', '', '', 50, 'Motivo recupero', 4, 'text', '', 1, '', '']
];

// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
//$daticrud['vincolicanc'] = [
//                            
//                            [('tbl_noteindalu'),'idalunno'],
//                            [('tbl_assenze'),'idlaunno']
//                            
//                           ];
$daticrud['vincolicanc'] = [
];

$daticrud['abilitazionemodifica'] = 1;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1, ''];


$_SESSION['daticrud'] = $daticrud;

header("location: ../crudtabelle/CRUD.php?suffisso=" . $_SESSION['suffisso']);



