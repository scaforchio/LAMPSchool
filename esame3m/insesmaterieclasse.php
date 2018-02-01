<?php session_start();

/*
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

//Visualizzazione classi
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

$idclasse = stringa_html('idclasse');


$titolo = "Inserimento materie scritti esame per classe";
$script = "";
stampa_head($titolo, "", $script, "E");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Inserimento materie scritti esame per classe", "", "$nome_scuola", "$comune_scuola");


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);

$m1s = stringa_html('m1s');
$m1e = stringa_html('m1e');
$m1m = stringa_html('m1m');
$m2s = stringa_html('m2s');
$m2e = stringa_html('m2e');
$m2m = stringa_html('m2m');
$m3s = stringa_html('m3s');
$m3e = stringa_html('m3e');
$m3m = stringa_html('m3m');
$m4s = stringa_html('m4s');
$m4e = stringa_html('m4e');
$m4m = stringa_html('m4m');
$m5s = stringa_html('m5s');
$m5e = stringa_html('m5e');
$m5m = stringa_html('m5m');
$m6s = stringa_html('m6s');
$m6e = stringa_html('m6e');
$m6m = stringa_html('m6m');
$m7s = stringa_html('m7s');
$m7e = stringa_html('m7e');
$m7m = stringa_html('m7m');
$m8s = stringa_html('m8s');
$m8e = stringa_html('m8e');
$m8m = stringa_html('m8m');
$m9s = stringa_html('m9s');
$m9e = stringa_html('m9e');
$m9m = stringa_html('m9m');
$secondalingua=stringa_html('secondalingua');
$invalsi=stringa_html('invalsi');
$query = "SELECT * FROM tbl_esmaterie where idclasse=$idclasse";
$ris = mysqli_query($con, inspref($query));


if (mysqli_num_rows($ris) != 0)
{

    // AGGIORNAMENTO

    $query = "UPDATE tbl_esmaterie SET 
              m1s='$m1s', m1e='$m1e', m1m='$m1m', 
              m2s='$m2s', m2e='$m2e', m2m='$m2m', 
              m3s='$m3s', m3e='$m3e', m3m='$m3m', 
              m4s='$m4s', m4e='$m4e', m4m='$m4m', 
              m5s='$m5s', m5e='$m5e', m5m='$m5m', 
              m6s='$m6s', m6e='$m6e', m6m='$m6m', 
              m7s='$m7s', m7e='$m7e', m7m='$m7m', 
              m8s='$m8s', m8e='$m8e', m8m='$m8m', 
              m9s='$m9s', m9e='$m9e', m9m='$m9m',
              num2lin='$secondalingua', numpni='$invalsi'
              where idclasse=$idclasse";
    mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));
}
else
{
    // INSERIMENTO
    $query = "INSERT INTO tbl_esmaterie(idclasse,m1s,m1e,m1m,m2s,m2e,m2m,m3s,m3e,m3m,m4s,m4e,m4m,m5s,m5e,m5m,m6s,m6e,m6m,m7s,m7e,m7m,m8s,m8e,m8m,m9s,m9e,m9m,num2lin,numpni)
              VALUES($idclasse,'$m1s','$m1e','$m1m','$m2s','$m2e','$m2m','$m3s','$m3e','$m3m','$m4s','$m4e','$m4m','$m5s','$m5e','$m5m','$m6s','$m6e','$m6m','$m7s','$m7e','$m7m','$m8s','$m8e','$m8m','$m9s','$m9e','$m9m','$secondalingua','$invalsi')";
    mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));


}


print "
			  <form method='post' id='formlez' action='../esame3m/esmaterieclasse.php'>

			  <input type='hidden' name='idclasse' value='$idclasse'>
			  <input type='hidden' name='reg' value='1'>
			  </form>
			  <SCRIPT language='JavaScript'>
			  {
				  document.getElementById('formlez').submit();
			  }
			  </SCRIPT>";

mysqli_close($con);
stampa_piede("");


