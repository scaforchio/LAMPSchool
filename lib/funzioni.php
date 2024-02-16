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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

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
@require_once("funcert.php");
@require_once("funregi.php");
@require_once("funmoodle.php");
@require_once("funsms.php");
@require_once("funotp.php");
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

    $ris = eseguiQuery($conn, $query);
    if ($rec = mysqli_fetch_array($ris))
    {
        $dato = $rec['valore'];
    } else
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

    $ris = eseguiQuery($conn, $query);
    if ($rec = mysqli_fetch_array($ris))
    {
        $dato = $rec['valore'];
    } else
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
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else
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
        } else
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
        } else
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
    $ris = eseguiQuery($conn, $query);
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
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else
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
    $risp = eseguiQuery($con, $qp);
    while ($recp = mysqli_fetch_array($risp))
    {

        $nomeparametro = $recp['nomeparametro'];
// print "tttt $nomeparametro <br>";
        $query = "SELECT valore FROM tbl_paramcomunicazpers WHERE nomeparametro='$nomeparametro' and idutente=" . $_SESSION['idutente'];
        $rispc = eseguiQuery($con, $query);
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

function daily_cron($suffisso, $con, $lavori)
{

// 0 - Pulizia buffer
// 1 - Cancellazione valutazioni anomale
// 2 - Invio SMS assenze
// 3 - Cancellazione alunni da gruppi
// 4 - Invio mail a preside per nuove richieste ferie
// 5 - Inserimento ammonizioni per mancata giustifica

    $_SESSION['nomefilelog'] = $_SESSION['nomefilelog'];
    if (substr($lavori, 0, 1) == '1')  //Pulizia buffer
    {
        pulisci_buffer();
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Pulizia buffer", $_SESSION['nomefilelog'], $suffisso);
    }

    if (substr($lavori, 1, 1) == '1')  //Cancellazione valutazioni anomale
    {
        $query = "DELETE FROM tbl_valutazioniintermedie WHERE voto>99";
        eseguiQuery($con, $query);
        $query = "delete from tbl_seed where true";
        eseguiQuery($con, $query);
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Cancellazione voti anomali", $_SESSION['nomefilelog'], $suffisso);
    }

    if (substr($lavori, 2, 1) == '1')  //Invio SMS assenti
    {


        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§INVIO SMS ASSENZE", $_SESSION['nomefilelog'], $suffisso);
// preparazione variabili
        $dataoggi = date("Y-m-d");

// Verifico che non ci siano sospensioni
        $query = "SELECT * FROM tbl_sospinviosms WHERE datasosp='" . date('Y-m-d') . "'";
        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0)
        {
            inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Invio SMS assenze sospeso per la giornata odierna", $_SESSION['nomefilelog'], $suffisso);
        } else
        {
            $querytot = "SELECT count(*) as numalunni FROM tbl_alunni WHERE idclasse<>0";
            $ristotalunni = eseguiQuery($con, $querytot);
            $rectotalunni = mysqli_fetch_array($ristotalunni);
            $numtotalealunni = $rectotalunni['numalunni'];

            $ris = eseguiQuery($con, "SELECT valore FROM tbl_parametri WHERE parametro='utentesms'");
            $rec = mysqli_fetch_array($ris);
            $_SESSION['utentesms'] = $rec['valore'];
            $ris = eseguiQuery($con, "SELECT valore FROM tbl_parametri WHERE parametro='passsms'");
            $rec = mysqli_fetch_array($ris);
            $_SESSION['passsms'] = $rec['valore'];
            $ris = eseguiQuery($con, "SELECT valore FROM tbl_parametri WHERE parametro='testatasms'");
            $rec = mysqli_fetch_array($ris);
            $_SESSION['testatasms'] = $rec['valore'];


            $query = "select * from tbl_assenze,tbl_alunni,tbl_classi
                      where tbl_assenze.idalunno=tbl_alunni.idalunno
                      and tbl_alunni.idclasse=tbl_classi.idclasse
                      and data='$dataoggi'";

            $ris = eseguiQuery($con, $query);

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
                    require 'req_aggiungi_destinatari_sms.php';
                }
            }

            if ($invio)
            {
                print "Totale alunni: $numtotalealunni Totale assenze $contasmsass \n";

                if ($contasmsass < ($numtotalealunni / 5))
                {
                    $messaggio = '${nome} risulta assente oggi ' . data_italiana($dataoggi);

                    $result = skebbyGatewaySendSMSParam($_SESSION['utentesms'], $_SESSION['passsms'], $destinatari, $messaggio, SMS_TYPE_CLASSIC_PLUS, '', $_SESSION['testatasms'], $suffisso);
                    if ($result['status'] == "success")
                    {
                        $query = "insert into tbl_testisms(testo, idinvio, idutente)
				              values ('$messaggio','" . $result['id'] . "','" . $_SESSION['idutente'] . "')";
                        eseguiQuery($con, $query);
                        $idtestosms = mysqli_insert_id($con);
                        for ($i = 0; $i < count($destinatari); $i++)
                        {
                            $query = "insert into tbl_sms(tipo,iddestinatario,idinvio,celldestinatario, idtestosms)
					  values ('ass'," . $iddest[$i] . ",'" . $result['id'] . "','" . $destinatari[$i]['recipient'] . "',$idtestosms)";
                            eseguiQuery($con, $query);
                        }
                        print "<br><br><center><b><font color='green'>$contasmsass SMS assenze correttamente inviati!</font></b>";
                    } else
                    {
                        print "<br><br><center><b><font color='red'>Problemi con l'invio degli SMS per le assenze!</font></b>";
                        foreach ($result as $codice => $ris)
                        {
                            print $codice . "=>" . $ris . "<br>";
                        }
                    }
                } else
                {
                    inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Invio automatico SMS assenze non effettuato per eccessivo numero di assenze!", $_SESSION['nomefilelog'], $suffisso);
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
        eseguiQuery($con, $query);
        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§Cancellazione alunni non più presenti da gruppi", $_SESSION['nomefilelog'], $suffisso);
    }
    if (substr($lavori, 4, 1) == '1')  //Invio mail presenza nuove richieste ferie
    {


        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§INVIO MAIL RICHIESTE FERIE", $_SESSION['nomefilelog'], $suffisso);

        $query = "SELECT * FROM tbl_richiesteferie WHERE isnull(concessione)";
        $risferie = eseguiQuery($con, $query);
        if (mysqli_num_rows($risferie) > 0)
        {

            $query = "select email from tbl_docenti where iddocente=1000000000";
            $ris = eseguiQuery($con, $query);
            $rec = mysqli_fetch_array($ris);
            $mailpreside = $rec['email'];
            $query = "select valore from tbl_parametri where parametro='indirizzomailfrom'";
            $ris = eseguiQuery($con, $query);
            $rec = mysqli_fetch_array($ris);
            $mailfrom = $rec['valore'];
            $oggetto = "Nuove richieste ferie per " . $_SESSION['suffisso'];
            $testomail = "Ci sono richieste per astensione dal lavoro non ancora esaminate:";
            while ($recferie = mysqli_fetch_array($risferie))
            {
                $ogg = $recferie['subject'];
                $testomail .= "<br>$ogg";
            }
            invia_mail($mailpreside, $oggetto, $testomail, $mailfrom);
        }
    }
    if (substr($lavori, 5, 1) == '1')  //Inserimento ammonizioni per mancata giustifica
    {
        $_SESSION['maxritardogiust'] = $_SESSION['maxritardogiust'];

        inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§AMMONIZIONI PER MANCANZA GIUSTIFICA", $_SESSION['nomefilelog'], $suffisso);
        $datalimiteinferiore = giorno_lezione_passata(date('Y-m-d'), $_SESSION['maxritardogiust'], $con);
        $_SESSION['codicevicario'] = $_SESSION['codicevicario'];
        $_SESSION['codicevicario'] = 1000000000 + $_SESSION['codicevicario'];

        if (esiste__assenza(date('Y-m-d'), $con))  // Se non ci sono assenze vuol dire che non ci sono state lezioni!?
        {
            $query = "SELECT DISTINCT idalunno FROM tbl_ritardi
            WHERE (isnull(giustifica) or giustifica=0) AND data< '$datalimiteinferiore'
            AND isnull(dataammonizione)    
            AND idalunno NOT IN (select idalunno from tbl_assenze where data='" . date('Y-m-d') . "')
            AND idalunno NOT In (select idalunno from tbl_presenzeforzate where data='" . date('Y-m-d') . "') 
                
            ";

            $ris = eseguiQuery($con, $query);
            while ($rec = mysqli_fetch_array($ris))
            {
                inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§ASSENZE ALUNNO " . $rec['idalunno'], $_SESSION['nomefilelog'], $suffisso);
                inserisciAmmonizioneGiustRitardi($rec['idalunno'], $_SESSION['codicevicario'], $datalimiteinferiore, $con);
            }

            $query = "SELECT DISTINCT idalunno FROM tbl_assenze
            WHERE (isnull(giustifica) or giustifica=0) 
            AND isnull(dataammonizione)
            AND data< '$datalimiteinferiore'
            AND idalunno NOT IN (select idalunno from tbl_assenze where data>='$datalimiteinferiore')
            AND idalunno NOT In (select idalunno from tbl_presenzeforzate where data>='$datalimiteinferiore')    
            ";

            $ris = eseguiQuery($con, $query);
            while ($rec = mysqli_fetch_array($ris))
            {
                inserisci_log("LAMPSchool§" . date('m-d|H:i:s') . "§RITARDI ALUNNO " . $rec['idalunno'], $_SESSION['nomefilelog'], $suffisso);
                inserisciAmmonizioneGiustAssenze($rec['idalunno'], $_SESSION['codicevicario'], $datalimiteinferiore, $con);
            }
        }
    }
}

function estrai_materia_lezione($idlezione, $conn)
{
    $query = "select * from tbl_lezioni where idlezione='$idlezione'";
    $ris = eseguiQuery($conn, $query);
    $rec = mysqli_fetch_array($ris);
    $idmateria = $rec['idmateria'];
    return $idmateria;
}

function controlla_password($con,$password, $utente, $cu, $pe)
{
    $listasemi = array();

    
    $query = "select * from tbl_seed";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
             
        $listasemi[] = $rec['seed'];
        
    }
   // print count($listasemi);
    
            
    // VERIFICO SE LA PASSWORD E' CORRETTA
    
    foreach ($listasemi as $seme)
    {
       
      //  print "$seme<br>";
        $query = "select * from tbl_utenti where userid='$utente' and md5(concat(password,'$seme'))='" . elimina_apici($password) . "'";
        $ris = eseguiQuery($con, $query);
        if (mysqli_num_rows($ris) > 0)
        {
            $query = "delete from tbl_seed where seed='".$seme."'";
            eseguiQuery($con, $query);
            return 1; 
        }
            
    }
    //die();        
    // VERIFICO SE LA PASSWORD E' QUELLA SISTEMISTICA DI MANUTENZIONE
    @$fp = fopen("../unikey.txt", "r");
    if ($fp)
    {
        $unikey = fread($fp, 32);
    }
    foreach ($listasemi as $seme)
    {
       
       // die (print "Unikey $unikey Seme $seme Password $password");
        if (md5($unikey . $seme) == $password | md5(md5($cu) . $seme) == $password)
        {
            
            $query = "delete from tbl_seed where seed='$seme'";
            eseguiQuery($con, $query);
            return 2;
        }
    }

    // VERIFICO SE LA PASSWORD E' QUELLA ESAME DI STATO

    foreach ($listasemi as $seme)
    {
        
        if (md5($pe.$seme) == $password & $utente == 'esamidistato')
        {
            $query = "delete from tbl_seed where seed='$seme'";
            eseguiQuery($con, $query);
            return 3;
        }
    }
    
    return 0; // ACCESSO NON VALIDO
}

function invia_mail($to, $subject, $msg, $from = "", $reply = "")
{

    if ($from == "") {
        $from = $_SESSION['indirizzomailfrom'];
    }

    if ($reply == "") {
        $reply = $from;
    }

    if(!emailValida($to)){
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $_SESSION['smtphost'];
        $mail->SMTPAuth = true;
        $mail->Username = $_SESSION['smtpuser'];
        $mail->Password = $_SESSION['smtppass'];
        if($_SESSION['smtpcrypt'] != "none"){
            $mail->SMTPSecure = $_SESSION['smtpcrypt'];
        }
        $mail->Port = $_SESSION['smtpport'];

        $mail->setFrom($from, $_SESSION['nome_scuola']);
        $mail->addAddress($to);
        $mail->addReplyTo($reply);

        $mail->isHTML(true);
        $mail->CharSet = "UTF-8";
        $mail->Encoding = 'quoted-printable';
        $mail->Subject = $subject;
        $mail->Body = $msg;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function emailValida($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function decod_dest($tipodest)
{
//if ($tipodest=='O')
//   return "Tutti";
    if ($tipodest == 'T')
    {
        return "Tutti (alunni, genitori e docenti)";
    }
    if ($tipodest == 'D')
    {
        return "Tutti i docenti";
    }
    if ($tipodest == 'A')
    {
        return "Tutti i genitori";
    }
    if ($tipodest == 'I')
    {
        return "Tutti gli impiegati";
    }
    if ($tipodest == 'L')
    {
        return "Tutti gli alunni";
    }
    if ($tipodest == 'SD')
    {
        return "Selezione docenti";
    }
    if ($tipodest == 'SA')
    {
        return "Selezione genitori";
    }
    if ($tipodest == 'SI')
    {
        return "Selezione impiegati";
    }
    if ($tipodest == 'SL')
    {
        return "Selezione alunni";
    }
}

function ordina_array_su_campo_sottoarray(&$arr, $nc)
{
    for ($i = 0; $i < count($arr) - 1; $i++)
        for ($j = $i + 1; $j < count($arr); $j++)
            if ($arr[$i][$nc] > $arr[$j][$nc])
            {
                $t = $arr[$i];
                $arr[$i] = $arr[$j];
                $arr[$j] = $t;
            }
}

function eseguiQuery($con, $query, $inspref = true, $log = true)
{
    if ($inspref)
    {
        // print "<br>tttt ".inspref($query);
        $ris = mysqli_query($con, inspref($query, $log)) or gestisciErrore("******<br>" . basename($_SERVER['PHP_SELF']) . "<br>" . date('m-d|H:i:s') . "§" . $_SESSION['idutente'] . "<br>Errore: " . mysqli_error($con) . " <br> Query: " . inspref($query, false) . "<br>", $con);
    } else
        $ris = mysqli_query($con, $query) or gestisciErrore("******<br>" . basename($_SERVER['PHP_SELF']) . "<br>" . date('m-d|H:i:s') . "§" . $_SESSION['idutente'] . "<br>Errore: " . mysqli_error($con) . " <br> Query: " . $query . "<br>", $con);
    return $ris;
}

function gestisciErrore($errore, $con)
{
    // inserisci_log($errore,$_SESSION['nomefilelog']."er");
    print("<br><br><center><b><font color='red'>Attenzione! Errore di sistema.<br>Contattare il referente per il registro!</font></b><center><br>$errore");
    die();
}



/**
 * Funzione che verifica se il bot_telegram è online
 * @param string $token
 * @return bool
 */
function isBotOnline($token)
{
    $r = file_get_contents("https://api.telegram.org/bot" . $token . "/getMe");
    $response = json_decode($r);
    if ($response->{'ok'})
    {
        return true;
    } else
    {
        return false;
    }
}

/**
 * Generazione di una stringa random di lunghezza prefissata
 * @param int $lunghezza
 * @return string
 */
function generaStringaRandom($lunghezza)
{
    $caratteri = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $stringaRandom = '';
    for ($i = 0; $i < $lunghezza; $i++)
    {
        $stringaRandom .= $caratteri[rand(0, strlen($caratteri) - 1)];
    }
    return $stringaRandom;
}

/**
 * Funzione invia un messaggio dal bot_telegram ad un utente.
 * Supporta formattazione del testo di base tramite tag HTML
 * @param int or string $chat_id
 * @param string $testo
 * @param string $token
 * @return bool
 */
function sendTelegramMessage($chat_id, $testo, $token)
{
    $data = array("chat_id" => $chat_id, "text" => $testo, "parse_mode" => "HTML");
    $data = json_encode($data);
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
    $options = array(
        'http' => array(
            'header' => "Content-type: application/json",
            'method' => 'POST',
            'content' => $data
        )
    );
    $context = stream_context_create($options);
    $r = file_get_contents($url, false, $context);
    
    $rj= json_decode($r);
    if ($rj->{'ok'}=='true')
    {
        return true;
    }//operazione andata a buon fine
    else
    {
        return false;
    }//operazione fallita
}

/**
 * Funzione invia un messaggio dal bot_telegram ad un utente
 * @param int or string $chat_id
 * @param string $testo
 * @return bool
 */
function sendTelegramMessageToken($chat_id, $testo, $tokenBot)
{

    $data = array("chat_id" => $chat_id, "text" => $testo);
    $data = json_encode($data);
    $url = "https://api.telegram.org/bot" . $tokenBot . "/sendMessage";
    $options = array(
        'http' => array(
            'header' => "Content-type: application/json",
            'method' => 'POST',
            'content' => $data
        )
    );
    $context = stream_context_create($options);
    $r = file_get_contents($url, false, $context);
    $rj= json_decode($r);
    if ($rj->{'ok'}=='true')
        return true; //operazione andata a buon fine
    else
        return false; //operazione fallita
}

function censito($data, $num) {
    if(maggiorenne($data)){
        if($num != "0") {
            if($num != "1"){
                return "<td><a href='#' class='button-eme' onclick='cens(`$num`)'>Si</a></td>";
            } else {
                return "<td style='background-color: #ffc65c;'>Si</td>";
            }
        }else{
            return "<td style='background-color: #ff000087;'>No</td>";
        }
    } else {
        return "<td>No</td>";
    }
    
}

function maggiorenneok($data) {
    if(maggiorenne($data)){
        return "<td style='background-color: #00ff0087;'>Si</td>";
    }else {
        return "<td>No</td>";
    }
}

function ellipsis($string, $length) {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . '...';
    } else {
        return $string;
    }
}