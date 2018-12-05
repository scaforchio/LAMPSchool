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
$daticrud['titolo']='GESTIONE CLASSI';


$daticrud['tabella'] = inspref("tbl_classi");


// Nome della tabella per visualizzazioni
$daticrud['aliastabella'] = "classi";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idclasse";

// Campi in base ai quali ordinare (specificare gli alias (14° valore nella descrizione del campo)
// se ci sono campi con lo stesso nome)
$daticrud['campiordinamento']= [inspref("anno,specializzazione,sezione")];
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione']= inspref("true");

$daticrud['abilitazionemodifica']=1;
$daticrud['abilitazionecancellazione']=0;
$daticrud['abilitazioneinserimento']=0;

// Dati per conferma cancellazione (0 senza conferma, 1 con conferma ed elenco dei campi da visualizzare per conferma)

$daticrud['confermacancellazione'] = [1,''];
// Vincoli per possibilità di cancellazione. Non devono esserci riferimenti nelle seguenti tabelle nel campo
// specificato
$daticrud['vincolicanc'] = [
                            
                            [inspref('tbl_alunni'),'idclasse'],
                            [inspref('tbl_competalu'),'idclasse'],
                            [inspref('tbl_competdoc'),'idclasse'],
                            [inspref('tbl_documenti'),'idclasse'],
                            [inspref('tbl_entrateclassi'),'idclasse'],
                            [inspref('tbl_esiti'),'idclasse'],
                            [inspref('tbl_esami3m'),'idclasse'],
                            [inspref('tbl_esmaterie'),'idclasse'],
                            [inspref('tbl_giudizi'),'idclasse'],
                            [inspref('tbl_lezioni'),'idclasse'],
                            [inspref('tbl_lezionicert'),'idclasse'],
                            [inspref('tbl_notealunno'),'idclasse'],
                            [inspref('tbl_noteclasse'),'idclasse'],
                            [inspref('tbl_osssist'),'idclasse'],
                            [inspref('tbl_scrutini'),'idclasse'],
                            [inspref('tbl_valutazionicomp'),'idclasse'],
                            [inspref('tbl_valutazioniintermedie'),'idclasse'],
                            [inspref('tbl_annotazioni'),'idclasse'],
                            [inspref('tbl_assemblee'),'idclasse'],
                            [inspref('tbl_cambiamenticlasse'),'idclasse'],
                            [inspref('tbl_cattnosupp'),'idclasse'],
                            [inspref('tbl_cattsupp'),'idclasse']
                            
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

$daticrud['campi'] = [
                      ['anno','1','','','',1,'Anno',1,'number','',1,'1',$numeroanni,0,'',1],
                      ['sezione','2',inspref('tbl_sezioni'),'denominazione','denominazione',1,'Sezione',2,'','',1,'','',1,'sezione',1],
                      ['specializzazione','3',inspref('tbl_specializzazioni'),'denominazione','denominazione',1,$plesso_specializzazione,3,'','',1,'','',1,'specializzazione',1],
                      ['oresett',4,'','','',2,"Ore settimanali",4,'number','',1,'20','48',0,'',1,''],
                      ['idcoordinatore',5,inspref('tbl_docenti'),'iddocente','cognome,nome',0,'Docente',5,'','',0,'','',1,'',1,inspref('select distinct(iddocente) from tbl_cattnosupp where idclasse=') ],
                      ['rappresentante1',6,inspref('tbl_alunni'),'idalunno','cognome,nome',0,'Primo rappresentante',6,'','',0,'','',0,'cognalu1,nomealu1',0,inspref('select distinct(idalunno) from tbl_alunni where idclasse=') ],
                      ['rappresentante2',7,inspref('tbl_alunni'),'idalunno','cognome,nome',0,'Secondo rappresentante',7,'','',0,'','',0,'cognalu2,nomealu2',0,inspref('select distinct(idalunno) from tbl_alunni where idclasse=')]
                     ];


$_SESSION['daticrud'] = $daticrud;

header("location: ../crudtabelle/CRUD.php?suffisso=".$_SESSION['suffisso']);

