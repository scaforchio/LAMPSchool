<?php

$suffisso = $_GET['suffisso'];

@include("../php-ini" . $suffisso . ".php");

@include("../lib/funzioni.php");

if ($suffisso != "")
{
    $suff = $suffisso . "/";
}
else
    $suff = "";
// Elimina tutti i messaggi di NOTICE
error_reporting(E_ALL ^ E_NOTICE);

$alunno = 0;
$cognome = "";
$nome = "";
$datanascita = "";
$idclasse = "";


$utente = stringa_html("utente");
$password = stringa_html("password");
$versione = stringa_html("versione");
$andver = stringa_html("andver");
$sorgente = stringa_html("sorgente");

//if ($suffisso == 'demo')
//{
inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tentato accesso da App Android $password Versione $versione Android $andver Sorgente $sorgente", $nomefilelog . "ap", $suff);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("errore connessione");


if ($password == $chiaveuniversale)
{
    $sql = "select * from tbl_utenti where userid='$utente'";
}
else
{
    $sql = "select * from tbl_utenti where userid='$utente' and  password=md5('$password')";
}

$result = mysqli_query($con, inspref($sql)) or die(inspref($sql, false));


if (!$val = mysqli_fetch_array($result))  // ALUNNO NON TROVATO
{
    $alunno = 0;
    die("Alunno non trovato!");
}
else
{
    if (time() - $val['ultimoaccessoapp'] > 60)   // RICHIESTA OK
    {
        $idutente = $val['idutente'];
        if ($idutente > 2100000000)
            $alunno = $idutente - 2100000000;
        else
            $alunno = $idutente;
        // AGGIORNO ULTIMO ACCESSO
        $sql = "UPDATE tbl_utenti SET ultimoaccessoapp=" . time() . " where idutente=$idutente";

        mysqli_query($con, inspref($sql)) or die(inspref($sql, false));

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
    else   // RICHIESTA DEGLI STESSI DATI EFFETTUATA PRIMA DI UN MINUTO
    {
        inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tempo basso ", $nomefilelog . "ap", $suff);
        sleep(10);
        inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Sbloccato ", $nomefilelog . "ap", $suff);
        die("Tempo basso");
    }
}

session_start();
$_SESSION['idutente'] = $idutente;

inserisci_log($utente . "§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso da App Android", $nomefilelog . "ap", $suff);

$q = "select data,tipo,voto,giudizio,denominazione from tbl_valutazioniintermedie,tbl_materie where idalunno='$alunno' and tbl_valutazioniintermedie.idmateria=tbl_materie.idmateria order by data desc, denominazione";

$r = mysqli_query($con, inspref($q));

$dataval = array();
$tipoval = array();
$votoval = array();
$giudizioval = array();
$denval = array();
$numerovoti = 0;
if (mysqli_num_rows($r) == 0)
{
    $dataval[] = data_italiana($datainiziolezioni);
    $tipoval[] = "";
    $votoval[] = "99";
    $giudizioval[] = "Inizio lezioni:";
    $denval[] = "";
    $numerovoti = 1;
}
else
{
    while ($row = mysqli_fetch_array($r))
    {

        $giudizio = $row['giudizio'];
        if (substr($giudizio, 0, 1) == "(")
        {
            $giudizio = "";
        }
        else
        {
            $giudizio = $row['giudizio'];
        }
        if ($row["voto"] != 99 | $giudizio != "")
        {
            $dataval[] = data_italiana($row["data"]);
            $tipoval[] = $row["tipo"];
            $votoval[] = $row["voto"];
            $giudizioval[] = $giudizio;
            $denval[] = $row["denominazione"];
        }
        $numerovoti++;
    }
}



$q = "select denominazione,argomenti,attivita,datalezione from tbl_lezioni,tbl_materie where tbl_lezioni.idmateria=tbl_materie.idmateria and idclasse='$idclasse' order by datalezione";

$r = mysqli_query($con, inspref($q));

$matelez = array();
$argolez = array();
$attilez = array();
$datelez = array();

while ($row = mysqli_fetch_array($r))
{
    $datelez[] = data_italiana($row["datalezione"]);
    $argolez[] = $row["argomenti"];
    $attilez[] = $row["attivita"];
    $matelez[] = $row["denominazione"];
}


// ESTRAZIONE COMUNICAZIONI
// Preleva gli oggetti, i testi e le date di pubblicazione degli avvisi
if (substr($utente, 0, 2) != "al")
{
    $data = date('Y-m-d');
    $query = "select oggetto,testo,inizio  from tbl_avvisi where destinatari like '%T%' and '$data' between inizio and fine ";
    $ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

    $oggetti = array();
    $testi = array();
    $datapub = array();
    $numerocomunicazioni = 0;
    while ($row = mysqli_fetch_array($ris))
    {
        $oggetti[] = $row['oggetto'];
        $testi[] = inserisci_parametri($row['testo'], $con);
        $datapub[] = data_italiana($row['inizio']);
        $numerocomunicazioni++;
    }
}
//ESTRAZIONE NOTE

$query = "select tbl_notealunno.testo,nome,cognome,data
			from tbl_notealunno,tbl_noteindalu,tbl_docenti
			where idalunno='$alunno' and tbl_notealunno.idnotaalunno = tbl_noteindalu.idnotaalunno and tbl_notealunno.iddocente=tbl_docenti.iddocente
			order by data, cognome, nome, testo ";

$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

$notealunno = array();
$nomed = array();
$cognomed = array();
$data3 = array();
$numeronote = 0;
while ($row = mysqli_fetch_array($ris))
{
    $notealunno[] = $row["testo"];
    $nomed[] = $row["nome"];
    $cognomed[] = $row["cognome"];
    $data3[] = data_italiana($row["data"]);
    $numeronote++;
}


$query = "select tbl_noteclasse.testo,nome,cognome,data
			from tbl_noteclasse,tbl_docenti
			where tbl_noteclasse.idclasse='$idclasse' and tbl_noteclasse.iddocente=tbl_docenti.iddocente
			order by data, cognome, nome, testo";

$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

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
    $numeronote++;
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
$numassenze = 0;
$numuscite = 0;
$numtitardi = 0;

// L'if seguente evita che venga inviata una segnalazione di nuova assenza quando la mattina viene inserita l'assenza in automatico
// dopo la ricezione della prima timbratura.
if (date("H:i") > "08:30")
{
    $query = "select data,giustifica from tbl_assenze where idalunno='$alunno' order by data desc";
}
else
{
    $query = "select data,giustifica from tbl_assenze where idalunno='$alunno' and data<'" . date("Y-m-d") . "' order by data desc";
}
$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
{
    $dateassenza[] = data_italiana($row["data"]);
    $giusta[] = $row["giustifica"];
    //$dateassenza[]=$row['data']."|".$row['giustifica'];
    $numassenze++;
}

$query = "select data,oraentrata,giustifica,numeroore from tbl_ritardi where idalunno='$alunno' order by data desc";
$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
{
    $dateritardi[] = data_italiana($row['data']);
    $orae[] = substr($row['oraentrata'], 0, 5);
    $numo[] = $row['numeroore'];
    $giustr[] = $row['giustifica'];
    //$dateritardi[]=$row['data']."|".$row['oraentrata']."|".$row['numeroore']."|".$row['giustifica'];
    $numritardi++;
}

$query = "select data,orauscita,numeroore from tbl_usciteanticipate where idalunno='$alunno' order by data desc";
$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

while ($row = mysqli_fetch_array($ris))
{
    $dateuscite[] = data_italiana($row['data']);
    $orau[] = substr($row['orauscita'], 0, 5);
    $numou[] = $row['numeroore'];
    //$dateuscite[]=$row['data']."|".$row['orauscita']."|".$row['numeroore'];
    $numuscite++;
}
$denclasse = decodifica_classe($idclasse, $con);

$arr = array('fineprimo' => $fineprimo, 'alunno' => $alunno, 'classe' => $denclasse, 'cognome' => $cognome, 'nome' => $nome, 'datanascita' => $datanascita, 'numeroassenze' => $numassenze, 'numeroritardi' => $numritardi, 'numerouscite' => $numuscite, 'numerovoti' => $numerovoti, 'numeronote' => $numeronote, 'numerocomunicazioni' => $numerocomunicazioni, 'oggcom' => $oggetti, 'datecom' => $datapub, 'testicom' => $testi, 'date' => $dataval, 'tipo' => $tipoval, 'voto' => $votoval, 'giudizio' => $giudizioval, 'denominazione' => $denval, 'notealunno' => $notealunno, 'nomedoc' => $nomed, 'cognomedoc' => $cognomed, 'data' => $data3, 'noteclasse' => $noteclasse, 'nomedc' => $nomedc, 'cognomedc' => $cognomedc, 'datac' => $datac, 'dateass' => $dateassenza, 'giustass' => $giusta, 'daterit' => $dateritardi, 'oraent' => $orae, 'numore' => $numo, 'giustr' => $giustr, 'dateusc' => $dateuscite, 'oraus' => $orau, 'numoreu' => $numou, 'matelez' => $matelez, 'datelez' => $datelez, 'argolez' => $argolez, 'attilez' => $attilez);
$strarr = json_encode($arr);

inserisci_log($utente . "§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Trasmessi dati a smartphone", $nomefilelog . "ap", $suff);

print $strarr;
