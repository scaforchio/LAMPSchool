<?php

require_once '../lib/req_apertura_sessione.php';

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
/* programma per l'inserimento di un docente
  riceve in ingresso i valori del docente */
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento docente";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='vis_doc.php'>ELENCO DOCENTI</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$iddocente = stringa_html('codice');
$cognome = stringa_html('cognome');
//print $cognome;
$nome = stringa_html('nome');
$aa = stringa_html('datadinasca')!=''?stringa_html('datadinasca'):'0001';
$gg = stringa_html('datadinascg')!=''?stringa_html('datadinascg'):'01';
$mm = stringa_html('datadinascm')!=''?stringa_html('datadinascm'):'01';

$comnasc = stringa_html('idcomn')!=''?stringa_html('idcomn'):'0';
$indirizzo = stringa_html('indirizzo');
$comresi = stringa_html('idcomr')!=''?stringa_html('idcomr'):'0';
$email = stringa_html('email');
$sostegno = stringa_html('sostegno');
$gestoremoodle = stringa_html('gestoremoodle');
$telefono = stringa_html('telefono');
$cellulare = stringa_html('telcel');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<H1>connessione al server mysql fallita</H1>");
    exit;
}
$DB = true;
if (!$DB)
{
    print("<H1>connessione al database stage fallita</H1>");
    exit;
}
// $query="insert into tbl_docenti (cognome,nome)values ('$cognome','$nome')";
// VERIFICO SE E' IL PRIMO DOCENTE, IN QUESTO CASO AGGIUNGO 1000000 ALL'IDDOCENTE

$que = "select * from tbl_docenti where iddocente>1000000000";
$res = eseguiQuery($con,$que);
if (mysqli_num_rows($res) == 0)
{
    $iddocente = 1000000001;

    $query = "insert into tbl_docenti (iddocente,cognome,nome,datanascita,idcomnasc,indirizzo,idcomres,telefono,telcel,email,sostegno,gestoremoodle,idutente) values ('$iddocente','$cognome','$nome','$aa-$mm-$gg','$comnasc','$indirizzo','$comresi','$telefono','$cellulare','$email','$sostegno','$gestoremoodle','$iddocente')";
} else
    $query = "insert into tbl_docenti (cognome,nome,datanascita,idcomnasc,indirizzo,idcomres,telefono,telcel,email,sostegno,gestoremoodle) values ('$cognome','$nome','$aa-$mm-$gg','$comnasc','$indirizzo','$comresi','$telefono','$cellulare','$email','$sostegno','$gestoremoodle')";
$err = 0;
$b = 0;
$flag = 0;
$mes = "";

if ($cognome == "")
{
    $err = 1;
    $mes = "Il cognome non è stato inserito <br/>";
} else
{
    if (controlla_stringa($cognome) == 1)
    {
        $err = 1;
        $mes = "Il cognome non può contenere valori numerici <br/>";
    }
}

if ($nome == "")
{
    $err = 1;
    $mes = $mes . " Il nome non è stato inserito <br/>";
} else
{
    if (controlla_stringa($nome) == 1)
    {
        $err = 1;
        $mes = "Il nome non può contenere valori numerici <br/>";
    }
}

if ($err == 1)
{
    print("<center><font size='3' color='red'><b>Correzioni:</b></font><br/>");
    print("$mes");
    print("<br/><form NAME='hid' action='ins_doc.php' method='POST'>");

    print(" <input type ='hidden' size='20' name='codi' value= '$iddocente'>");
    print(" <input type ='hidden' size='20' name='cog' value= '$cognome'>");
    print(" <input type ='hidden' size='20' name='no' value= '$nome'>");

    print(" <input type ='hidden' size='2'maxlength='2' name='datag' value=$gg><input type ='hidden' size='2' maxlength='2'name='datam' value=$mm><input type ='hidden' size='4' maxlength='4'name='dataa' value=$aa>");
    print(" <input type ='hidden' size='20' name='idcomn' value= '$comnasc'>");
    print(" <input type ='hidden' size='20' name='idcomr' value= '$comresi'>");

    print(" <input type ='hidden' size='20' name='ind' value= '$indirizzo'> ");
    print("  <input type ='hidden' size='20' name='tel' value= '$telefono'>");
    print(" <input type ='hidden' size='20' name='telc' value= '$cellulare'>");
    print(" <input type ='hidden' size='20' name='em' value= '$email'>");
    print(" <input type ='hidden' size='20' name='flag' value= '1'>");
    print("<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>");
    print("</form></center>");
} else
{
    $res = eseguiQuery($con, $query);

    if (!$res)
    {
        print("<h2>Il docente non &eacute; stato inserito</h2>$query");
    } else
    {
        $iddocenteinserito = mysqli_insert_id($con);
        // Aggiorno l'idutente del docente
        $query = "update tbl_docenti set idutente=$iddocenteinserito where iddocente=$iddocenteinserito";
        if (!$res = eseguiQuery($con, $query))
            die("Errore aggiornamento id utente del docente!");
        // INSERISCO ANCHE IL RECORD NELLA TABELLA DEGLI tbl_utenti
        $utente = "doc" . ($iddocenteinserito - 1000000000);
        $utentemoodle = "doc" . $_SESSION['suffisso'] . ($iddocenteinserito - 1000000000);
        $password = creapassword();
        $sqlt = "insert into tbl_utenti(idutente,userid,password,tipo) values ('$iddocenteinserito','$utente',md5('" . md5($password) . "'),'D')";
        $res = eseguiQuery($con, $sqlt);
        if ($_SESSION['tokenservizimoodle'] != '')
        {

            if ($email == "")
                $email = $usernamedocente . "@dominioemailfittizio.it";

            $esito = creaUtenteMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $utentemoodle, $password, $cognome, $nome, $email);
            print "<br>Esito: $esito";
            if ((strstr($esito, $utentemoodle) > -1))
            {
                print "<br> Inserito utente Moodle: $cognome $nome $usernamedocente $password $email";
            }


            $idmoodle = getIdMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $utentemoodle);
            //print "IDMOODLE $idmoodle";
            cambiaPasswordMoodle($_SESSION['tokenservizimoodle'], $_SESSION['urlmoodle'], $idmoodle, $utentemoodle, $password);
        }
        // print "risultato inserimento $iddocenteinserito<br/>"; 
        print "<FONT SIZE='+2'><CENTER>Inserimento eseguito</CENTER></FONT>";
        print "<p align='center'>Dati di autenticazione per $nome $cognome";
        print "<br/>Utente: $utente<br/>Password:$password </p>";
        print "<br><br><center>";
        print "<form target='_blank' name='stampa' action='stampa_pass_doc.php' method='POST'>
                   <input type='hidden' name='iddoc1' value='$iddocenteinserito'> 
                   <input type='hidden' name='utdoc1' value='$utente'> 
                   <input type='hidden' name='pwdoc1' value='$password'> 
                   <input type='hidden' name='numpass' value='1'> 
                   
                   <input type='submit' value='STAMPA'>
                   </form>";
        print "</center>";
    }
}

mysqli_close($con);
stampa_piede("");

