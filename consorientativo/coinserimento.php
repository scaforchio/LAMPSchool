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

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

$idclasse= stringa_html("idclasse");


// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Inserimento consigli orientativi";
$script = "";
stampa_head($titolo, "", $script, "SPD");






$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// Cancello le vecchie proposte


// Inserisco le nuove proposte per ogni competenza presente

$query = "select idalunno from tbl_alunni where idclasse=$idclasse";

$ris = mysqli_query($con, inspref($query)) or die("Errore nella query: " . mysqli_error($con));

while ($rec = mysqli_fetch_array($ris)) {
    $idalunno=$rec['idalunno'];
    $nomecampo="co_$idalunno";
    $coalunno=stringa_html($nomecampo);
    $query="delete from tbl_consorientativi where idalunno=$idalunno";
    mysqli_query($con,inspref($query)) or die("Errore".inspref($query));
    $query="insert into tbl_consorientativi (idalunno, consiglioorientativo) values ($idalunno,'$coalunno')";
    mysqli_query($con,inspref($query)) or die("Errore".inspref($query));
    
}

print "
			  <form method='post' id='formcc' action='./cotabellone.php'>
			  
			  <input type='hidden' name='idclasse' value='$idclasse'>
			  <br><div style=\"text-align: center;\"><input type='submit' value='OK'></div>
			  </form>
                          <SCRIPT language='JavaScript'>
			  {
				  document.getElementById('formcc').submit();
			  }
			  </SCRIPT>";
			  

mysqli_close($con);
stampa_piede("");

