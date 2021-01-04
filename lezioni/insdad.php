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


/*
  Programma per l'inserimento delle tbl_cattnosupp
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

$titolo = "Inserimento giornate d.a.d.";
$script = "";
stampa_head($titolo, "", $script, "MASDP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='dad.php'>Inserimento giornate D.A.D.</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$datainizio= data_to_db(stringa_html('datainizio'));
$datafine=data_to_db(stringa_html('datafine'));
$idclassi=stringa_html('idclassi');
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


if ($datainizio < $_SESSION['datainiziolezioni'])  // Per evitare errori di inserimento 
    $data = $_SESSION['datainiziolezioni'];
else
    $data = $datainizio;
if ($datafine > $_SESSION['datafinelezioni'])  // Per evitare errori di inserimento 
    $datafine = $_SESSION['datafinelezioni'];

do
{
    

   
    foreach ($idclassi as $idclasse)
    {
    if ((!giorno_festa($data, $con)) && (giorno_settimana($data) != "Dom"))
    {
        $query="delete from tbl_dad where datadad='$data' and idclasse=$idclasse";
        eseguiQuery($con, $query);
        $query = "insert into tbl_dad(idclasse, datadad) values ($idclasse,'$data')";
        eseguiQuery($con, $query);
        print "<br>Inserita giornata di d.a.d. il ".data_italiana($data)." per la classe ". decodifica_classe($idclasse, $con);
    }
    }
    $data = aggiungi_giorni($data, 1);
} while ($data <= $datafine);



mysqli_close($con);
stampa_piede("");

