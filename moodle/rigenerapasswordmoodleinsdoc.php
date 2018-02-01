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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Password Moodle";
$script = "<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>";
stampa_head($titolo, "", $script,"MSP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");




// Array per procedura di stampa
$numpass = 0;
$arr_id = "";
$arr_ut = "";
$arr_pw = "";

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$query = "SELECT iddocente as al,cognome, nome FROM tbl_docenti order by cognome, nome";

$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
print "<center><br><b><big>Password Moodle Docenti</big></b></center><br>";
print "<table border='1' align='center'>";
print "<tr class='prima'><td>Docente</td><td>Username</td><td>Password</td></tr>";
while ($id = mysqli_fetch_array($ris))
{
    $cambiamento=false;
    $strch='rig' . $id['al'];

    $idal = stringa_html($strch) ? "on" : "off";
    if ($idal == "on")
    {
        $nuovapassword=creapassword();

        $username="doc".$_SESSION['suffisso'].($id['al']-1000000000);


        $arr_id .= $id['al']."|";
        $arr_ut .= "$username|";
        $arr_pw .= "$nuovapassword|";
        $numpass++;


        $idutentemoodle=getIdMoodle($tokenservizimoodle,$urlmoodle,$username);

        cambiaPasswordMoodle($tokenservizimoodle,$urlmoodle,$idutentemoodle,$username,$nuovapassword);

        $cambiamento=true;
        print "<tr><td>".$id['cognome']." ".$id['nome']."</td>";
        print "<td>$username</td><td> $nuovapassword</td></tr>";

    }

}
print "</table>";
print "<center><img src='../immagini/stampa.png' onClick='printPage();'></center>";




// print "<center><br><br><a href='./rigenerapasswordmoodledoc.php'>Indietro</a>";
stampa_piede("");

