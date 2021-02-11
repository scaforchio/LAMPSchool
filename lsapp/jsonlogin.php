<?php
session_start();
$suffisso = $_GET['suffisso'];

@include("../php-ini" . $suffisso . ".php");

@include("../lib/funzioni.php");
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("errore connessione");
mysqli_set_charset( $con, 'utf8');
require "../lib/req_assegna_parametri_a_sessione.php";
if ($suffisso != "")
{
    $suff = $suffisso . "/";
} else
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
// inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tentato accesso da App Android $password Versione $versione Android $andver Sorgente $sorgente", $_SESSION['nomefilelog'] . "ap", $suff);



if ($password == $_SESSION['chiaveuniversale'])
{
    $sql = "select * from tbl_utenti where userid='$utente'";
} else
{
    $sql = "select * from tbl_utenti where userid='$utente' and  password=md5('$password')";
}

$result = eseguiQuery($con, $sql);


if (!$val = mysqli_fetch_array($result))  // ALUNNO NON TROVATO
{
    $alunno = 0;
    inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tentato accesso errato da App Android $password Versione $versione Android $andver Sorgente $sorgente", $_SESSION['nomefilelog'] . "ap", $suff);

    die("Alunno non trovato!");
} else
{
    if (((time() - $val['ultimoaccessoapp']) > 60) | ($sorgente != 2))
    //if (true)// RICHIESTA OK
    {
        inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§TIME " . time() . " ULTIMO " . $val['ultimoaccessoapp'], $_SESSION['nomefilelog'] . "ap", $suff);

        $idutente = $val['idutente'];
        if ($idutente > 2100000000)
            $alunno = $idutente - 2100000000;
        else
            $alunno = $idutente;
        
        // AGGIORNO ULTIMO ACCESSO
        $sql = "UPDATE tbl_utenti SET ultimoaccessoapp=" . time() . " where idutente=$idutente";
        eseguiQuery($con, $sql);
        inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Aggiornato ultimo accesso ", $_SESSION['nomefilelog'] . "ap", $suff);

        $sql = "SELECT * FROM tbl_alunni WHERE idalunno='" . $alunno . "'";
        $ris2 = eseguiQuery($con, $sql);

        if ($val2 = mysqli_fetch_array($ris2))
        {
            $cognome = $val2["cognome"];
            $nome = $val2["nome"];
            $datanascita = $val2["datanascita"];
            $idclasse = $val2["idclasse"];
            
        }
    } else   // RICHIESTA DEGLI STESSI DATI EFFETTUATA PRIMA DI UN MINUTO
    {
        inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Tempo basso ", $_SESSION['nomefilelog'] . "ap", $suff);
        sleep(60);
        inserisci_log($utente . "§" . date('m-d|H:i:s') . "§" . IndirizzoIpReale() . "§Sbloccato ", $_SESSION['nomefilelog'] . "ap", $suff);
        die("Tempo basso");
    }
}
$datelez[] = data_italiana($row["datalezione"]);
            $argolez[] = $row["argomenti"];
            $attilez[] = $row["attivita"];
            $matelez[] = $row["denominazione"];
session_start();
$_SESSION['idutente'] = $idutente;

inserisci_log($utente . "§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Accesso da App Android", $_SESSION['nomefilelog'] . "ap", $suff);


$dataval = array();
$tipoval = array();
$votoval = array();
$giudizioval = array();
$denval = array();

$numerovoti = 0;

$materie = array();
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
$numeromaterie = 0;
$matelez = array();
$argolez = array();
$attilez = array();
$datelez = array();

$oggetti = array();
$testi = array();
$datapub = array();
$numerocomunicazioni = 0;

$notealunno = array();
$nomed = array();
$cognomed = array();
$data3 = array();

$numeronote = 0;

$noteclasse = array();
$nomedc = array();
$cognomedc = array();
$datac = array();
if ($_SESSION['gensolocomunicazioni'] != 'yes')
{
    $q = "select distinct denominazione from tbl_cattnosupp, tbl_materie "
            . " where tbl_cattnosupp.idmateria=tbl_materie.idmateria and idclasse=$idclasse";

    $r = eseguiQuery($con, $q);
    if (mysqli_num_rows($r) != 0)
    {
        while ($row = mysqli_fetch_array($r))
        {

            $materia = $row['denominazione'];
            $materie[]=$materia;
            $numeromaterie++;
            
        }
    }

    
    
    $q = "select data,tipo,voto,giudizio,denominazione from tbl_valutazioniintermedie,tbl_materie where idalunno='$alunno' and tbl_valutazioniintermedie.idmateria=tbl_materie.idmateria order by data desc, denominazione";

    $r = eseguiQuery($con, $q);
    if (mysqli_num_rows($r) == 0)
    {
        $dataval[] = data_italiana($_SESSION['datainiziolezioni']);
        $tipoval[] = "";
        $votoval[] = "99";
        $giudizioval[] = "Inizio lezioni:";
        $denval[] = "";
        $numerovoti = 1;
         
    } else
    {
        while ($row = mysqli_fetch_array($r))
        {

            $giudizio = $row['giudizio'];
            if (substr($giudizio, 0, 1) == "(")
            {
                $giudizio = "";
            } else
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



    $q = "select denominazione,argomenti,attivita,datalezione,idlezionegruppo from tbl_lezioni,tbl_materie where tbl_lezioni.idmateria=tbl_materie.idmateria and idclasse='$idclasse' order by datalezione";

    $r = eseguiQuery($con, $q);



    while ($row = mysqli_fetch_array($r))
    {
        if ($row["idlezionegruppo"] == 0 | $row["idlezionegruppo"] == NULL)
        {
            $datelez[] = data_italiana($row["datalezione"]);
            $argolez[] = $row["argomenti"];
            $attilez[] = $row["attivita"];
            $matelez[] = $row["denominazione"];
            
        } else
        {
            $queryricercagruppo = "select idgruppo from tbl_lezionigruppi where idlezionegruppo=" . $row["idlezionegruppo"];
            $rislezgruppo = eseguiQuery($con, $queryricercagruppo);
            $reclezgruppo = mysqli_fetch_array($rislezgruppo);
            $idgruppo = $reclezgruppo["idgruppo"];
            $queryverificaappgruppo = "select * from tbl_gruppialunni where idgruppo=$idgruppo and idalunno=$alunno";
            $risalugruppo = eseguiQuery($con, $queryverificaappgruppo);
            if (mysqli_num_rows($risalugruppo) > 0)
            {
                $datelez[] = data_italiana($row["datalezione"]);
                $argolez[] = $row["argomenti"];
                $attilez[] = $row["attivita"];
                $matelez[] = $row["denominazione"];
                
            }
            
        }
    }


// ESTRAZIONE COMUNICAZIONI
// Preleva gli oggetti, i testi e le date di pubblicazione degli avvisi


    if (substr($utente, 0, 2) != "al")
    {
        $data = date('Y-m-d');
        $query = "select oggetto,testo,inizio  from tbl_avvisi where destinatari like '%T%' and '$data' between inizio and fine ";
        $ris = eseguiQuery($con, $query);


        while ($row = mysqli_fetch_array($ris))
        {
            $oggetti[] = $row['oggetto'];
            $testi[] = inserisci_parametri($row['testo'], $con);
            $datapub[] = data_italiana($row['inizio']);
            $numerocomunicazioni++;
        }
    } else
    {
        $data = date('Y-m-d');
        $query = "select oggetto,testo,inizio  from tbl_avvisi where destinatari like '%L%' and '$data' between inizio and fine ";
        $ris = eseguiQuery($con, $query);


        while ($row = mysqli_fetch_array($ris))
        {
            $oggetti[] = $row['oggetto'];
            $testi[] = inserisci_parametri($row['testo'], $con);
            $datapub[] = data_italiana($row['inizio']);
            $numerocomunicazioni++;
        }
    }
// Estrazione comunicazioni da annotazioni
    $datalimiteinferiore = aggiungi_giorni(date('Y-m-d'), -5);
    if (substr($utente, 0, 2) != "al")
        $query = "select * from tbl_annotazioni,tbl_docenti
                where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                    and idclasse=$idclasse
                    and data>'$datalimiteinferiore'
                    and visibilitagenitori=true";
    else
        $query = "select * from tbl_annotazioni,tbl_docenti
                where tbl_annotazioni.iddocente=tbl_docenti.iddocente
                    and idclasse=$idclasse
                    and data>'$datalimiteinferiore'
                    and visibilitaalunni=true";

    /*
      $query = "select * from tbl_annotazioni
      where idclasse=$idclasse
      and data>DATE_ADD(data, INTERVAL -5 DAY)
      and visibilitagenitori=true";
     */

    $ris = eseguiQuery($con, $query);
    if (mysqli_num_rows($ris) > 0)
    {
        while ($rec = mysqli_fetch_array($ris))
        {
            $oggetti[] = "ANNOTAZIONE";
            $testi[] = $rec['testo']." (".$rec['cognome']." ".$rec['nome'].")";
            $datapub[] = data_italiana($rec['data']);
            $numerocomunicazioni++;
        }
    }


//ESTRAZIONE NOTE

    $query = "select tbl_notealunno.testo,nome,cognome,data
			from tbl_notealunno,tbl_noteindalu,tbl_docenti
			where idalunno='$alunno' and tbl_notealunno.idnotaalunno = tbl_noteindalu.idnotaalunno and tbl_notealunno.iddocente=tbl_docenti.iddocente
			order by data, cognome, nome, testo ";

    $ris = eseguiQuery($con, $query);


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

    $ris = eseguiQuery($con, $query);


    while ($row = mysqli_fetch_array($ris))
    {
        $noteclasse[] = $row["testo"];
        $nomedc[] = $row["nome"];
        $cognomedc[] = $row["cognome"];
        $datac[] = data_italiana($row["data"]);
        $numeronote++;
    }

// ESTRAGGO ASSENZE RITARDI E USCITE ANTICIPATE
// L'if seguente evita che venga inviata una segnalazione di nuova assenza quando la mattina viene inserita l'assenza in automatico
// dopo la ricezione della prima timbratura.
    if (date("H:i") > "08:30")
    {
        $query = "select data,giustifica from tbl_assenze where idalunno='$alunno' order by data desc";
    } else
    {
        $query = "select data,giustifica from tbl_assenze where idalunno='$alunno' and data<'" . date("Y-m-d") . "' order by data desc";
    }
    $ris = eseguiQuery($con, $query);

    while ($row = mysqli_fetch_array($ris))
    {
        if ($row["data"] == NULL)
            $dateassenza[] = data_italiana("0000-00-00");
        else
            $dateassenza[] = data_italiana($row["data"]);
        if ($row["giustifica"] == NULL)
            $giusta[] = 0;
        else
            $giusta[] = $row["giustifica"];
        //$giusta[] = $row["giustifica"];
        //$dateassenza[]=$row['data']."|".$row['giustifica'];
        $numassenze++;
    }

    $query = "select data,oraentrata,giustifica,numeroore from tbl_ritardi where idalunno='$alunno' order by data desc";
    $ris = eseguiQuery($con, $query);

    while ($row = mysqli_fetch_array($ris))
    {
        $dateritardi[] = data_italiana($row['data']);
        $orae[] = substr($row['oraentrata'], 0, 5);
        $numo[] = $row['numeroore'];
        if ($row["giustifica"] == NULL)
            $giustr[] = 0;
        else
            $giustr[] = $row["giustifica"];
        //$giustr[] = $row['giustifica'];
        //$dateritardi[]=$row['data']."|".$row['oraentrata']."|".$row['numeroore']."|".$row['giustifica'];
        $numritardi++;
    }

    $query = "select data,orauscita,numeroore from tbl_usciteanticipate where idalunno='$alunno' order by data desc";
    $ris = eseguiQuery($con, $query);

    while ($row = mysqli_fetch_array($ris))
    {
        $dateuscite[] = data_italiana($row['data']);
        $orau[] = substr($row['orauscita'], 0, 5);
        $numou[] = $row['numeroore'];
        //$dateuscite[]=$row['data']."|".$row['orauscita']."|".$row['numeroore'];
        $numuscite++;
    }
}
$denclasse = decodifica_classe($idclasse, $con);
 
$arr = array('fineprimo' => $_SESSION['fineprimo'], 'alunno' => $alunno, 'classe' => $denclasse, 'cognome' => $cognome, 'nome' => $nome, 'datanascita' => $datanascita, 'numeroassenze' => $numassenze, 'numeroritardi' => $numritardi,'numeromaterie' => $numeromaterie, 'numerouscite' => $numuscite, 'numerovoti' => $numerovoti, 'numeronote' => $numeronote, 'numerocomunicazioni' => $numerocomunicazioni, 'oggcom' => $oggetti, 'datecom' => $datapub, 'testicom' => $testi, 'date' => $dataval, 'tipo' => $tipoval, 'voto' => $votoval, 'giudizio' => $giudizioval, 'denominazione' => $denval, 'notealunno' => $notealunno, 'nomedoc' => $nomed, 'cognomedoc' => $cognomed, 'data' => $data3, 'noteclasse' => $noteclasse, 'nomedc' => $nomedc, 'cognomedc' => $cognomedc, 'datac' => $datac, 'dateass' => $dateassenza, 'giustass' => $giusta, 'daterit' => $dateritardi, 'oraent' => $orae, 'numore' => $numo, 'giustr' => $giustr, 'dateusc' => $dateuscite, 'oraus' => $orau, 'numoreu' => $numou, 'matelez' => $matelez, 'datelez' => $datelez, 'argolez' => $argolez, 'attilez' => $attilez, 'materie'=>$materie);

//print_r ($arr);

$strarr = json_encode($arr);


// inserisci_log($utente . "§" . date('m-d|H:i:s') . " §" . IndirizzoIpReale() . "§Trasmessi dati a smartphone", $_SESSION['nomefilelog'] . "ap", $suff);

print $strarr;
