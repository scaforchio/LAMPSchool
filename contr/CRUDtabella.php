<?php

session_start();

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login 
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}
$titolo = "TEST CRUD";
$script = "";
stampa_head($titolo, "", $script, "PMSDA");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);



$daticrud = array();
// Tabella da modificare
$daticrud['tabella'] = inspref("tbl_alunni");
$daticrud['aliastabella'] = "Alunni";
// Campo con l'id univoco per la tabella
$daticrud['campochiave'] = "idalunno";
// Campi da visualizzare senza chiave esterna



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

$daticrud['campi'] = [
                      ['cognome','1','','','',30,'Cognome',1,'text','',1,'',''],
                      ['nome','2','','','',30,'Nome',2,'text','',1,'',''],
                      ['idcomres','3',inspref('tbl_comuni'),'idcomune','denominazione',0,'Comune di residenza',3,'','',1,'','' ],
                      ['idclasse','4',inspref('tbl_classi'),'idclasse','anno,sezione,specializzazione',0,'Classe',4,'','',1,'',''],
                      ['telcel',0,'','','',20,'Telefono cellulare',5,'date','Massimo numeri separati da , o da +.',0,'','']
                     ];



// Campi in base ai quali ordinare
$daticrud['campiordinamento']= array("cognome","nome");
// Condizione di selezione, specificare solo 'true' se non ce ne sono
$daticrud['condizione']= inspref("tbl_alunni.idclasse<>0");
$_SESSION['daticrud'] = $daticrud;

ordina_array_su_campo_sottoarray($daticrud['campi'], 1);

require "CRUD.php";

stampa_piede();


/*
// creaGruppoGlobaleMoodle($tokenservizimoodle,$urlmoodle, "5ainf2017", "5ainf2017");
AggiungiGruppoClasse($con, $tokenservizimoodle, $urlmoodle, 28,$annoscol);
 

 $esito=invia_mail("pietro.tamburrano@gmail.com", "Prova", "Mail di prova.");
//$esito=mail("pietro.tamburrano@gmail.com", "Conferma", "prova","From: scaforchio@gmail.com");  
print "Esito $esito";
$esito=mail("pietro.tamburrano@gmail.com", "Conferma", "prova","From: lampschool@isdimaggio.it");  
print "Esito $esito";
stampa_piede("");

function AggiungiGruppoClasse($con,$token,$urlmoodle,$idclasse,$annoscol)
{
    $annocl=decodifica_anno_classe($idclasse, $con);
    $sezicl=decodifica_classe_sezione($idclasse, $con);
    $speccl= substr(decodifica_classe_spec($idclasse, $con),0,3);
    $identgruppo= strtolower($annocl.$sezicl.$speccl.$annoscol);
    $queryalunni="select idalunno from tbl_alunni where idclasse='$idclasse'";
    $res=mysqli_query($con, inspref($queryalunni)) or die("Errore $queryalunni");
    while ($rec=mysqli_fetch_array($res))
    {
        $idalunno=$rec['idalunno'];
        $username= costruisciUsernameMoodle($idalunno);
        aggiungiUtenteAGruppoGlobale($token, $urlmoodle, $identgruppo, $username);

        
    }
}
 * 
 */