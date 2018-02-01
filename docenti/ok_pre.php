<?php session_start();

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
/*programma per l'inserimento di un docente
riceve in ingresso i valori del docente*/
@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento preside";
$script = "";
stampa_head($titolo, "", $script,"PMSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$iddocente = stringa_html('codice');
$cognome = stringa_html('cognome');
$nome = stringa_html('nome');
$aa = stringa_html('datadinasca');
$gg = stringa_html('datadinascg');
$mm = stringa_html('datadinascm');

$comnasc = stringa_html('idcomn');
$indirizzo = stringa_html('indirizzo');
$comresi = stringa_html('idcomr');
$email = stringa_html('email');
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

// VERIFICO SE ESISTE GIA' il PRESIDE

$que = "SELECT * FROM tbl_docenti WHERE iddocente=1000000000";
$res = mysqli_query($con, inspref($que));
if (mysqli_num_rows($res) != 0)
{
    print("<CENTER>Preside già presente!</CENTER>");
    print("<FORM ACTION='../login/ele_ges.php' method='POST'>");
    print("<center>");
    print("<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>");
    print("</FORM></CENTER></BODY>");
}
else
{
    $iddocente = 1000000000;
    $query = "insert into tbl_docenti (iddocente,cognome,nome,datanascita,idcomnasc,indirizzo,idcomres,telefono,telcel,email,idutente)values ('$iddocente','$cognome','$nome','$aa-$mm-$gg','$comnasc','$indirizzo','$comresi','$telefono','$cellulare','$email','$iddocente')";
    $err = 0;
    $b = 0;
    $flag = 0;
    $mes = "";

    if ($cognome == "")
    {
        $err = 1;
        $mes = "Il cognome non &egrave; stato inserito <br/>";
    }
    else
    {
        if (controlla_stringa($cognome) == 1)
        {
            $err = 1;
            $mes = "Il cognome non pu&ograve; contenere valori numerici <br/>";
        }
    }

    if ($nome == "")
    {
        $err = 1;
        $mes = $mes . " Il nome non &egrave; stato inserito <br/>";
    }
    else
    {
        if (controlla_stringa($nome) == 1)
        {
            $err = 1;
            $mes = "Il nome non pu&ograve; contenere valori numerici <br/>";
        }
    }

    if ($err == 1)
    {
        print("<center><font size='3' color='red'><b>Correzioni:</b></font></center>");
        print("$mes");
        print("<FORM NAME='hid' action='ins_doc.php' method='POST'>");

        print(" <input type ='hidden' size='20' name='codi' value= '$iddocente'>");
        print(" <input type ='hidden' size='20' name='cog' value= '$cognome'>");
        print(" <input type ='hidden' size='20' name='no' value= '$nome'>");
        print(" <input type ='hidden' size='2'maxlength='2' name='datag' value=$gg>
                <input type ='hidden' size='2' maxlength='2'name='datam' value=$mm>
                <input type ='hidden' size='4' maxlength='4'name='dataa' value=$aa>");
        print(" <input type ='hidden' size='20' name='idcomn' value= '$comnasc'>");
        print(" <input type ='hidden' size='20' name='idcomr' value= '$comresi'>");
        print(" <input type ='hidden' size='20' name='ind' value= '$indirizzo'> ");
        print("  <input type ='hidden' size='20' name='tel' value= '$telefono'>");
        print(" <input type ='hidden' size='20' name='telc' value= '$cellulare'>");
        print(" <input type ='hidden' size='20' name='em' value= '$email'>");
        print(" <input type ='hidden' size='20' name='flag' value= '1'>");
        print("<INPUT TYPE='SUBMIT' VALUE='<< Indietro'>");
        print("</form><br/>");


    }
    else
    {
        $res = mysqli_query($con, inspref($query));
        if (!$res)
        {
            print("<h2>Il preside non &egrave; stato inserito</h2>$query");
        }
        else
        {
            $iddocenteinserito = mysqli_insert_id($con);

            // INSERISCO ANCHE IL RECORD NELLA TABELLA DEGLI tbl_utenti
            $utente = "preside";
            $password = creapassword();
            $sqlt = "insert into tbl_utenti(idutente,userid,password,tipo) values ('1000000000','$utente',md5('" . md5($password) . "'),'P')";
            $res = mysqli_query($con, inspref($sqlt));


            print("<b><br/><center>Utente: <i>$utente</i><br/>Password: <i>$password</i> </b><br/>");


            print("<h2>Il preside è stato correttamente inserito</h2></center>");

        }
    }
}
stampa_piede("");
mysqli_close($con);

