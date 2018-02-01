<?php

//session_start();


/*
  Funzioni di utilità

  Copyright (C) 2015 Pietro Tamburrano
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

error_reporting(E_ALL & ~E_NOTICE);

@require_once("funtest.php");
@require_once("fundate.php");
@require_once("funstri.php");
@require_once("funvoti.php");
@require_once("funcomu.php");
@require_once("funcatt.php");
@require_once("funscru.php");
@require_once("funalun.php");
@require_once("funcanc.php");
@require_once("fundoce.php");
@require_once("funclas.php");
@require_once("funasse.php");
@require_once("funmoodle.php");
// @require_once("funregi.php"); incluso solo nei due programmi che lo utilizzano

/**
 * Funzione che verifica la presenza di duplicati in un array
 * @param string $vettore
 * @return int
 */
function duplicati($vettore)
{
    $dimensione = count($vettore);

    sort($vettore);

    $dupl = 0;
    for ($i = 0; $i < ($dimensione - 1); $i++)
    {
        $j = $i + 1;
        if ($vettore[$i] == $vettore[$j])
            $dupl = 1;
    }

    return $dupl;
}

/**
 *
 * @param int $idalunno , int $idmateria
 * @param object $conn Connessione al db
 * @return char
 */
function estrai_testo($tipotesto, $conn)
{
    $query = "select valore from tbl_testi where nometesto='$tipotesto'";

    $ris = mysqli_query($conn, inspref($query)) or die("Errore nella query: " . mysqli_error($conn) . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $dato = $rec['valore'];
    }
    else
    {
        $dato = "";
    }
    return $dato;
}

/**
 *
 * @param int $idalunno , int $idmateria
 * @param object $conn Connessione al db
 * @return char
 */
function estrai_testo_modificato($tipotesto, $parametro, $valore, $conn)
{
    $query = "select valore from tbl_testi where nometesto='$tipotesto'";

    $ris = mysqli_query($conn, inspref($query)) or die("Errore nella query: " . mysqli_error($conn) . inspref($query));
    if ($rec = mysqli_fetch_array($ris))
    {
        $dato = $rec['valore'];
    }
    else
    {
        $dato = "";
    }
    $dato = str_replace($parametro, $valore, $dato);
    return $dato;
}

/**
 *
 * @return type
 */
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 *
 * @param string $dirpath
 * @param string $ext
 */
function svuota_cartella($dirpath, $ext)
{
    $handle = opendir($dirpath);
    while (($file = readdir($handle)) !== false)
    {
        if (substr($file, -strlen($ext)) == $ext)
        {
            echo "Cancellato: " . $file . "<br/>";
            @unlink($dirpath . $file);
        }
        else
        {
            echo "NON cancellato: " . $file . "<br/>";
        }
    }
    closedir($handle);
}

/**
 * Controllo del codice fiscale
 *
 * @param string $cf
 * @return boolean
 */
function Verifica_CodiceFiscale($cf)
{
    if ($cf == '')
    {
        return false;
    }
    if (strlen($cf) != 16)
    {
        return false;
    }
    $cf = strtoupper($cf);
    if (!preg_match("/[A-Z0-9]+$/", $cf))
    {
        return false;
    }
    $s = 0;
    for ($i = 1; $i <= 13; $i += 2)
    {
        $c = $cf[$i];
        if ('0' <= $c and $c <= '9')
        {
            $s += ord($c) - ord('0');
        }
        else
        {
            $s += ord($c) - ord('A');
        }
    }
    for ($i = 0; $i <= 14; $i += 2)
    {
        $c = $cf[$i];
        switch ($c)
        {
            case '0':
                $s += 1;
                break;
            case '1':
                $s += 0;
                break;
            case '2':
                $s += 5;
                break;
            case '3':
                $s += 7;
                break;
            case '4':
                $s += 9;
                break;
            case '5':
                $s += 13;
                break;
            case '6':
                $s += 15;
                break;
            case '7':
                $s += 17;
                break;
            case '8':
                $s += 19;
                break;
            case '9':
                $s += 21;
                break;
            case 'A':
                $s += 1;
                break;
            case 'B':
                $s += 0;
                break;
            case 'C':
                $s += 5;
                break;
            case 'D':
                $s += 7;
                break;
            case 'E':
                $s += 9;
                break;
            case 'F':
                $s += 13;
                break;
            case 'G':
                $s += 15;
                break;
            case 'H':
                $s += 17;
                break;
            case 'I':
                $s += 19;
                break;
            case 'J':
                $s += 21;
                break;
            case 'K':
                $s += 2;
                break;
            case 'L':
                $s += 4;
                break;
            case 'M':
                $s += 18;
                break;
            case 'N':
                $s += 20;
                break;
            case 'O':
                $s += 11;
                break;
            case 'P':
                $s += 3;
                break;
            case 'Q':
                $s += 6;
                break;
            case 'R':
                $s += 8;
                break;
            case 'S':
                $s += 12;
                break;
            case 'T':
                $s += 14;
                break;
            case 'U':
                $s += 16;
                break;
            case 'V':
                $s += 10;
                break;
            case 'W':
                $s += 22;
                break;
            case 'X':
                $s += 25;
                break;
            case 'Y':
                $s += 24;
                break;
            case 'Z':
                $s += 23;
                break;
        }
    }
    if (chr($s % 26 + ord('A')) != $cf[15])
    {
        return false;
    }
    return true;
}

/**
 *
 * @param int $idlezione
 * @param object $conn Connessione al db
 * @return string
 */
function estrai_lezione_gruppo($idlezione, $conn)
{
    $query = "select idlezionegruppo from tbl_lezioni where idlezione='$idlezione'";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore nella query: " . mysqli_error($conn) . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $idlezionegruppo = $rec['idlezionegruppo'];

    return $idlezionegruppo;
}

function verifica_numero_sms_residui($utesms, $passms)
{
    return skebbyGatewayGetCredit($utesms, $passms, 'UTF-8');
    /*
     * Restituisce:
      ['status'] success
      ['credit_left'] 4.77364
      ['classic_sms'] 74
      ['basic_sms'] 103
     */
}

function IndirizzoIpReale()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function pulisci_buffer()
{

    $filebuffer = Array();

    $f = opendir('../buffer/');
    while (false !== ($file = readdir($f)))
    {
        $filebuffer[] = $file;
    }
    closedir($f);
    foreach ($filebuffer as $fil)
    {
        if ($fil != "." & $fil != ".." & $fil != "index.html")
        {
            unlink("../buffer/" . $fil);
        }
    }
}

function inserisci_parametri($messaggio, $con)
{

    $qp = "SELECT DISTINCT nomeparametro FROM tbl_paramcomunicazpers";
    $risp = mysqli_query($con, inspref($qp));
    while ($recp = mysqli_fetch_array($risp))
    {

        $nomeparametro = $recp['nomeparametro'];
        // print "tttt $nomeparametro <br>";
        $query = "SELECT valore FROM tbl_paramcomunicazpers WHERE nomeparametro='$nomeparametro' and idutente=" . $_SESSION['idutente'];
        $rispc = mysqli_query($con, inspref($query));
        $valper = "";
        if ($recpf = mysqli_fetch_array($rispc))
        {
            $valper = $recpf['valore'];
        }

        $messaggio = str_replace("[$nomeparametro]", $valper, $messaggio);

        return $messaggio;
    }
    return $messaggio;
}

function daily_cron($suffisso, $con, $lavori, $nomefilelog)
{

    // 0 - Pulizia buffer
    // 1 - Cancellazione valutazioni anomale
    // 2 - Invio SMS assenze
    // 3 - Cancellazione alunni da gruppi
    // 4 - Invio mail a preside per nuove richieste ferie
    
    
    if (substr($lavori, 0, 1) == '1')  //Pulizia buffer
    {
        pulisci_buffer();
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Pulizia buffer", $nomefilelog);
    }
    
    if (substr($lavori, 1, 1) == '1')  //Cancellazione valutazioni anomale
    {
        $query = "DELETE FROM tbl_valutazioniintermedie WHERE voto>99";
        mysqli_query($con, inspref($query)) or die("Errore " . inspref($query));
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Cancellazione voti anomali", $nomefilelog);
    }
    
    if (substr($lavori, 2, 1) == '1')  //Invio SMS assenti
    {


        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§INVIO SMS ASSENZE", $nomefilelog);
        // preparazione variabili
        $dataoggi = date("Y-m-d");

        // Verifico che non ci siano sospensioni
        $query = "SELECT * FROM tbl_sospinviosms WHERE datasosp='" . date('Y-m-d') . "'";
        $ris = mysqli_query($con, inspref($query));
        if (mysqli_num_rows($ris) > 0)
        {
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Invio SMS assenze sospeso per la giornata odierna", $nomefilelog);
        }
        else
        {
            $querytot = "SELECT count(*) as numalunni FROM tbl_alunni WHERE idclasse<>0";
            $ristotalunni = mysqli_query($con, inspref($querytot));
            $rectotalunni = mysqli_fetch_array($ristotalunni);
            $numtotalealunni = $rectotalunni['numalunni'];

            $ris = mysqli_query($con, inspref("SELECT valore FROM tbl_parametri WHERE parametro='utentesms'"));
            $rec = mysqli_fetch_array($ris);
            $utentesms = $rec['valore'];
            $ris = mysqli_query($con, inspref("SELECT valore FROM tbl_parametri WHERE parametro='passsms'"));
            $rec = mysqli_fetch_array($ris);
            $passsms = $rec['valore'];
            $ris = mysqli_query($con, inspref("SELECT valore FROM tbl_parametri WHERE parametro='testatasms'"));
            $rec = mysqli_fetch_array($ris);
            $testatasms = $rec['valore'];


            $query = "select * from tbl_assenze,tbl_alunni,tbl_classi
                      where tbl_assenze.idalunno=tbl_alunni.idalunno
                      and tbl_alunni.idclasse=tbl_classi.idclasse
                      and data='$dataoggi'";

            $ris = mysqli_query($con, inspref($query));

            $destinatari = array();


            $iddest = array();
            $contasmsass = 0;
            $invio = false;
            while ($rec = mysqli_fetch_array($ris))
            {
                $idalunno = $rec['idalunno'];
                $idclasse = $rec['idclasse'];

                $asspre = verifica_ass_pre($idalunno, $dataoggi, $con);

                $telcel = VerificaCellulare($rec['telcel']);
                print "<br>Alunno: " . $idalunno;

                if (!$asspre & $telcel != "")
                {

                    $dest = array();
                    $destinatarialunno = array();
                    $destinatarialunno = explode(",", $rec['telcel']);
                    foreach ($destinatarialunno as $destalu)     // AGGIUNGE UN INVIO PER OGNI CELLULARE
                    {
                        $dest['recipient'] = "39" . trim($destalu); // .$rec['telcel'];
                        $dest['nome'] = $rec['nome'] . " " . $rec['cognome'];
                        $iddest[] = $rec['idalunno'];
                        $destinatari[] = $dest;
                        $contasmsass++;
                        $invio = true;
                    }
                }
            }

            if ($invio)
            {
                print "Totale alunni: $numtotalealunni Totale assenze $contasmsass \n";

                if ($contasmsass < ($numtotalealunni / 5))
                {
                    $messaggio = '${nome} risulta assente oggi ' . data_italiana($dataoggi);

                    // foreach ($destinatari as $d)
                    //     foreach ($d as $a)
                    //        print $a;
                    // print ("$utentesms , $passsms , ". $destinatari. ", $messaggio , SMS_TYPE_CLASSIC_PLUS, '', $testatasms, $suffisso");
                    $result = skebbyGatewaySendSMSParam($utentesms, $passsms, $destinatari, $messaggio, SMS_TYPE_CLASSIC_PLUS, '', $testatasms, $suffisso);
                    if ($result['status'] == "success")
                    {
                        $query = "insert into tbl_testisms(testo, idinvio, idutente)
				              values ('$messaggio','" . $result['id'] . "','" . $_SESSION['idutente'] . "')";
                        mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
                        $idtestosms = mysqli_insert_id($con);
                        for ($i = 0; $i < count($destinatari); $i++)
                        {
                            $query = "insert into tbl_sms(tipo,iddestinatario,idinvio,celldestinatario, idtestosms)
					  values ('ass'," . $iddest[$i] . ",'" . $result['id'] . "','" . $destinatari[$i]['recipient'] . "',$idtestosms)";
                            mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
                        }
                        print "<br><br><center><b><font color='green'>$contasmsass SMS assenze correttamente inviati!</font></b>";
                    }
                    else
                    {
                        print "<br><br><center><b><font color='red'>Problemi con l'invio degli SMS per le assenze!</font></b>";
                        foreach ($result as $codice => $ris)
                        {
                            print $codice . "=>" . $ris . "<br>";
                        }
                    }
                }
                else
                {
                    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Invio automatico SMS assenze non effettuato per eccessivo numero di assenze!", $nomefilelog);
                }
            }
        }
    }
    if (substr($lavori, 3, 1) == '1')  //Cancellazione dai gruppi degli alunni senza classe o cancellati
    {
        $query = "DELETE FROM tbl_gruppialunni
                  WHERE
                  idalunno in (select idalunno from tbl_alunni where idclasse=0)
                  OR
                  idalunno not in (select idalunno from tbl_alunni)";
        mysqli_query($con, inspref($query)) or die("Errore " . inspref($query));
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Cancellazione alunni non più presenti da gruppi", $nomefilelog);
    }
    if (substr($lavori, 4, 1) == '1')  //Invio mail presenza nuove richieste ferie
    {


        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§INVIO MAIL RICHIESTE FERIE", $nomefilelog);
        
        $query = "SELECT * FROM tbl_richiesteferie WHERE isnull(concessione)";
        $risferie = mysqli_query($con, inspref($query));
        if (mysqli_num_rows($risferie) > 0)
        {
            
            $query="select email from tbl_docenti where iddocente=1000000000";
            $ris=mysqli_query($con,inspref($query));
            $rec=mysqli_fetch_array($ris);
            $mailpreside=$rec['email'];
            $query="select valore from tbl_parametri where parametro='indirizzomailfrom'";
            $ris=mysqli_query($con,inspref($query));
            $rec=mysqli_fetch_array($ris);
            $mailfrom=$rec['valore'];
            $oggetto="Nuove richieste ferie per ".$_SESSION['suffisso'];
            $testomail="Ci sono richieste per astensione dal lavoro non ancora esaminate:";
            while($recferie=mysqli_fetch_array($risferie))
            {
                $ogg=$recferie['subject'];
                $testomail.="<br>$ogg";
            }
            invia_mail($mailpreside, $oggetto, $testomail,$mailfrom);
            
        }
    }    
}

function estrai_materia_lezione($idlezione, $conn)
{
    $query = "select * from tbl_lezioni where idlezione=$idlezione";
    $ris = mysqli_query($conn, inspref($query)) or die("Errore nella query: " . inspref($query));
    $rec = mysqli_fetch_array($ris);
    $idmateria = $rec['idmateria'];
    return $idmateria;
}

function invia_mail($to, $subject, $msg, $from = "", $reply = "")
{
    
      if ($from=="")
      $from=$_SESSION['indirizzomailfrom'];
      if ($reply=="")
      $reply=$from;
      $intestazioni  = "MIME-Version: 1.0\r\n";
      $intestazioni .= "Content-type: text/html; charset=utf8-general-ci\r\n";
      $intestazioni .= "From: " .$from. "\r\n";
      // $intestazioni .= "Reply-To: ".$reply."\r\n";

      $inviata=mail($to,$subject,$msg,$intestazioni);
      return $inviata;
     
    
}
