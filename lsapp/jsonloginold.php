<?php

$suffisso = $_GET['suffisso'];

@include("../php-ini" . $suffisso . ".php");

@include("../lib/funzioni.php");

// Elimina tutti i messaggi di NOTICE
error_reporting(E_ALL ^ E_NOTICE);

$alunno = 0;
$cognome = "";
$nome = "";
$datanascita = "";
$idclasse = "";


$utente = stringa_html("utente");
$password = stringa_html("password");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("errore connessione");


$sql = "select * from tbl_utenti where userid='$utente' and  password=md5(md5('$password'))";

$result = mysqli_query($con, inspref($sql)) or die(inspref($sql, false));

if (!$val = mysqli_fetch_array($result))
{
    $alunno = 0;
}
else
{
    $idutente = $val['idutente'];
    $alunno = $val['idutente'];

    $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $alunno . "'";
    $ris2 = mysqli_query($con, inspref($sql)) or die(inspref($sql, false));
    if ($val2 = mysqli_fetch_array($ris2))
    {
        $cognome = $val2["cognome"];
        $nome = $val2["nome"];
        $datanascita = $val2["datanascita"];
        $idclasse = $val2["idclasse"];
    }
}

session_start();
$_SESSION['idutente'] = $idutente;
if ($suffisso != "")
{
    $suff = $suffisso . "/";
}
else $suff = "";
inserisci_log($utente . "§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso da App Android",$nomefilelog,$suff);


$query = "select count(*) as numeroassenze from tbl_assenze where idalunno='$alunno'";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
    $numassenze = $row["numeroassenze"];

$query = "select count(*) as numeroritardi from tbl_ritardi where idalunno='$alunno'";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
    $numritardi = $row["numeroritardi"];


// conteggio uscite anticipate
$query = "select count(*) as numerouscite from tbl_usciteanticipate where idalunno='$alunno'";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
    $numuscite = $row["numerouscite"];

// conteggio voti
$query = "select count(*) as numerovoti from tbl_valutazioniintermedie where idalunno='$alunno'";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
    $numerovoti = $row["numerovoti"];

// conteggio note
$query = "select count(*) as numeronote from tbl_noteindalu where idalunno='$alunno'";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
    $numeronote = $row["numeronote"];


// conteggio comunicazioni

$data = date('Y-m-d'); // Data corrente nel formato AAAA:MM:GG

$query = "select count(*) as numerocomunicazioni from tbl_avvisi where destinatari like '%T%' and '$data' between inizio and fine";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
    $numerocomunicazioni = $row["numerocomunicazioni"];

// ESTRAZIONE VALUTAZIONI


$q = "select data,tipo,voto,giudizio,denominazione from tbl_valutazioniintermedie,tbl_materie where idalunno='$alunno' and tbl_valutazioniintermedie.idmateria=tbl_materie.idmateria order by data desc, denominazione";

$r = mysqli_query($con, inspref($q));

$dataval = array();
$tipoval = array();
$votoval = array();
$giudizioval = array();
$denval = array();
while ($row = mysqli_fetch_array($r))
{
    $dataval[] = data_italiana($row["data"]);
    $tipoval[] = $row["tipo"];
    $votoval[] = $row["voto"];
    $giudizioval[] = $row["giudizio"];
    $denval[] = $row["denominazione"];
}


// ESTRAZIONE COMUNICAZIONI

// Preleva gli oggetti, i testi e le date di pubblicazione degli avvisi

$data = date('Y-m-d');
$query = "select oggetto,testo,inizio  from tbl_avvisi where destinatari like '%T%' and '$data' between inizio and fine ";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

$oggetti = array();
$testi = array();
$datapub = array();
while ($row = mysqli_fetch_array($ris))
{
    $oggetti[] = $row['oggetto'];
    $testi[] = inserisci_parametri($row['testo'], $con);
    $datapub[] = data_italiana($row['inizio']);
}


//ESTRAZIONE NOTE

$query = "select tbl_notealunno.testo,nome,cognome,data
			from tbl_notealunno,tbl_noteindalu,tbl_docenti 
			where idalunno='$alunno' and tbl_notealunno.idnotaalunno = tbl_noteindalu.idnotaalunno and tbl_notealunno.iddocente=tbl_docenti.iddocente  ";

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

$notealunno = array();
$nomed = array();
$cognomed = array();
$data3 = array();
while ($row = mysqli_fetch_array($ris))
{
    $notealunno[] = $row["testo"];
    $nomed[] = $row["nome"];
    $cognomed[] = $row["cognome"];
    $data3[] = data_italiana($row["data"]);
}


$query = "select tbl_noteclasse.testo,nome,cognome,data
			from tbl_noteclasse,tbl_docenti 
			where tbl_noteclasse.idclasse='$idclasse' and tbl_noteclasse.iddocente=tbl_docenti.iddocente";

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

$noteclasse = array();
$nomedc = array();
$cognomedc = array();
$datac = array();
while ($row = mysqli_fetch_array($ris))
{
    $noteclasse[] = $row["testo"];
    $nomedc[] = $row["nome"];
    $cognomedc[] = $row["cognome"];
    $datac[] = data_italiana($row["data"]);
}

// ESTRAGGO ASSENZE RITARDI E USCITE ANTICIPATE


$dateassenza = array();
$dateritardi = array();
$dateuscite = array();
$orae = array();
$numo = array();
$giustr = array();
$orau = array();
$numou = array();
$query = "select data,giustifica from tbl_assenze where idalunno='$alunno' order by data desc";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
{
    $dateassenza[] = data_italiana($row["data"]);
    $giusta[] = $row["giustifica"];
    //$dateassenza[]=$row['data']."|".$row['giustifica'];

}


$query = "select data,oraentrata,giustifica,numeroore from tbl_ritardi where idalunno='$alunno' order by data desc";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
{
    $dateritardi[] = data_italiana($row['data']);
    $orae[] = $row['oraentrata'];
    $numo[] = $row['numeroore'];
    $giustr[] = $row['giustifica'];
    //$dateritardi[]=$row['data']."|".$row['oraentrata']."|".$row['numeroore']."|".$row['giustifica'];
}


$query = "select data,orauscita,numeroore from tbl_usciteanticipate where idalunno='$alunno' order by data desc";
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
{
    $dateuscite[] = data_italiana($row['data']);
    $orau[] = $row['orauscita'];
    $numou[] = $row['numeroore'];
    //$dateuscite[]=$row['data']."|".$row['orauscita']."|".$row['numeroore'];
}

$arr = array('alunno' => $alunno, 'classe' => $idclasse, 'cognome' => $cognome, 'nome' => $nome, 'datanascita' => $datanascita, 'numeroassenze' => $numassenze, 'numeroritardi' => $numritardi, 'numerouscite' => $numuscite, 'numerovoti' => $numerovoti, 'numeronote' => $numeronote, 'numerocomunicazioni' => $numerocomunicazioni, 'oggcom' => $oggetti, 'datecom' => $datapub, 'testicom' => $testi, 'date' => $dataval, 'tipo' => $tipoval, 'voto' => $votoval, 'giudizio' => $giudizioval, 'denominazione' => $denval, 'notealunno' => $notealunno, 'nomedoc' => $nomed, 'cognomedoc' => $cognomed, 'data' => $data3, 'noteclasse' => $noteclasse, 'nomedc' => $nomedc, 'cognomedc' => $cognomedc, 'datac' => $datac, 'dateass' => $dateassenza, 'giustass' => $giusta, 'daterit' => $dateritardi, 'oraent' => $orae, 'numore' => $numo, 'giustr' => $giustr, 'dateusc' => $dateuscite, 'oraus' => $orau, 'numoreu' => $numou);

$strarr = json_encode($arr);
inserisci_log($utente . "§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Trasmessi dati a smartphone",$nomefilelog,$suff);
print $strarr;
    

