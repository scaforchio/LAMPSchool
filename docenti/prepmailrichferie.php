<?php

session_start();

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma é distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
//require_once '../lib/ db / query.php';
//$lQuery = LQuery::getIstanza();
// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$iddocente = $_SESSION["idutente"];

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Richiesta astensione dal lavoro - testo mail";
$script = "";
stampa_head($titolo, "", $script, "MSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// $nominativo= estrai_dati_docente($_SESSION['idutente'], $con);
$nominativo = stringa_html('nominativo');
$datainizio = stringa_html('datainizio');
$datafine = stringa_html('datafine');
$numerogiorni = stringa_html('numerogiorni');
$tempo = stringa_html('tempo');
$to = $indirizzomailassenze;
$reason = stringa_html('reason');
if ($reason!=7)
    $subject = "Richiesta astensione di " . stringa_html('nominativo') . " da " . stringa_html('datainizio') . " a " . stringa_html('datafine');
else
    $subject = "Richiesta permesso breve di " . stringa_html('nominativo') . " giorno " . stringa_html('giornopermessobreve') . " da " . stringa_html('orainiziopermessobreve'). " a " . stringa_html('orafinepermessobreve');
print "<center><font color='red'>La richiesta inoltrata sarà la seguente.<br>Per inoltrarla premere il tasto [INOLTRA] in fondo alla pagina!<br></center></font><br>";

print "Oggetto: $subject";

$testomail = " Il/La sottoscritto/a <b>$nominativo</b>,<br>
			
				in servizio presso codesto istituto in qualit&agrave; di DOCENTE a tempo $tempo <br><br>
				
                        <center> CHIEDE </center><br>";
if ($reason != '7')
    $testomail .= "alla S.V. di assentarsi per n. <b>$numerogiorni gg dal $datainizio al $datafine</b> per:<br>";
else
    $testomail .= "alla S.V. di assentarsi per :<br>";

switch ($reason)
{
    case '0': $motivo = "Ferie ";
        break;
    case '1': $motivo = "Permesso retribuito (ai sensi art. 15 CCNL) per " . stringa_html('motivopermesso');
        break;
    case '2': 
             if(stringa_html('motivomalattia')=='Generica')
                 $motivo = "Malattia (ai sensi art. 17 CCNL).";
             else
                 $motivo = "Malattia (ai sensi art. 17 CCNL) per " . stringa_html('motivomalattia');
        break;
    case '3': $motivo = "Maternit&agrave; per " . stringa_html('motivomaternita');
        break;
    case '4': $motivo = "Aspettativa (ai sensi art. 18 CCNL) per " . stringa_html('motivoaspettativa');
        break;
    case '5': $motivo = "Legge 104/92 (Giorni gi&agrave; fruiti nel mese: " . stringa_html('giorniprecedenti104') . ")";
        break;
    case '6': $motivo = "Altro caso previsto dalla normativa vigente: " . stringa_html('altromotivo');
        break;
    case '7': $motivo = "Permesso breve per il giorno " . stringa_html('giornopermessobreve') . " dalle ore " . stringa_html('orainiziopermessobreve') . "  alle ore " . stringa_html('orafinepermessobreve') . ", per un totale di ore " . stringa_html('orepermessobreve') . ", (orario di servizio nella giornata pari a ore " . stringa_html('oreserviziopermessobreve') . ").";
        break;
}
$recapito = "<br><br>Durante il periodo di assenza sar&agrave; domiciliato in " . stringa_html('comunedomicilio') . ", alla via " . stringa_html('indirizzodomicilio') . " n." . stringa_html('numerodomicilio') . ", Tel. " . stringa_html('telefonorecapito');
if (stringa_html('allegati')!='')
    $allegati = "<br>Si allega: " . stringa_html('allegati')."<br>";
else
    $allegati="";
$testomail .= "<br><b>" . $motivo."</b>";
$testomail .= "$recapito<br>";
$testomail .= "$allegati";
$testomail .= "<br>";
$testomail .= "$comune_scuola , " . date("d/m/Y") . " <br>";
$testomail .= "<br><br>";
$testomail .= "<center>IN FEDE<br>$nominativo<br></center>";

print "<br><br>".$testomail;
print "<br><form action='inviamailrichferie.php' method='post'>"
        . "<input type='hidden' name='subject' value='$subject'>"
        . "<input type='hidden' name='testomail' value='$testomail'>";
print "<center><input type='submit' value='Inoltra'><br>";

mysqli_close($con);
stampa_piede("");
