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

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$iddocente = stringa_html('iddocente');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Disponibilità ricevimento genitori";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri) 
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "TDASPM");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$idclasse = stringa_html("idclasse");

// scelta classe
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

if ($_SESSION['tipoutente'] != 'T')
{
    print ("
				<form method='post' action='visdisponibilita.php' name='programmi'>
				<input type='hidden' name='suffisso' value='$suffisso'>
				<p align='center'>
				<table align='center'>");

    print("
			  <tr>
			  <td width='50%'><b>Classe</b></p></td>
			  <td width='50%'>
			  <SELECT NAME='idclasse' ONCHANGE='programmi.submit()'><option value=''></option>  ");

    //
    //  Riempimento combobox delle tbl_classi
    //
	$query = "select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi order by anno,sezione,specializzazione";
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    while ($nom = mysqli_fetch_array($ris))
    {
        print "<option value='";
        print ($nom["idclasse"]);
        print "'";
        if ($idclasse == $nom["idclasse"])
            print " selected";
        print ">";
        print ($nom["anno"]);
        print "&nbsp;";
        print($nom["sezione"]);
        print "&nbsp;";
        print($nom["specializzazione"]);
    }

    print ("</select></td></tr></table></form>");
}
if ($idclasse != "")
    $query = "select distinct cognome,nome,tbl_docenti.iddocente from tbl_cattnosupp,tbl_docenti
	        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	        and tbl_cattnosupp.idclasse=   " . $idclasse . "
	        and tbl_cattnosupp.iddocente!=1000000000
	        order by cognome,nome";
else
    $query = "select distinct cognome,nome,tbl_docenti.iddocente from tbl_cattnosupp,tbl_docenti
	        where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
	        
	        and tbl_cattnosupp.iddocente!=1000000000
	        order by cognome,nome";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
if ($_SESSION['tipoutente'] == 'T')
    print "<table border=1 align=center><tr class='prima'><td>Docente</td><td>Ricevimento</td><td>Appuntamento</td></tr>";
else
    print "<table border=1 align=center><tr class='prima'><td>Docente</td><td>Ricevimento</td></tr>";
while ($nom = mysqli_fetch_array($ris))
{


    print "<tr><td>" . $nom['cognome'] . " " . $nom['nome'];
    if ($idclasse != "")
    {
        //print "<br>";
        $query = "select idmateria from tbl_cattnosupp
			         where idclasse=$idclasse and iddocente=" . $nom['iddocente'] .
                " and idalunno=0";
        // print inspref($query);        
        $rismat = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
        print "<small>";
        while ($recmat = mysqli_fetch_array($rismat))
        {
            print "<br>" . decodifica_materia($recmat['idmateria'], $con) . "  ";
        }
        print "</small>";
    }

    print "</td>";
    print "<td>";
    $query = "select giorno,inizio, fine, note from tbl_orericevimento,tbl_orario
		         where tbl_orericevimento.idorario=tbl_orario.idorario
		         and iddocente=" . $nom['iddocente'] .
            " and tbl_orericevimento.valido 
		         order by giorno,inizio,fine";

    $ris2 = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    while ($nom2 = mysqli_fetch_array($ris2))
    {
        print giornodanum($nom2['giorno']) . " " . substr($nom2['inizio'], 0, 5) . "-" . substr($nom2["fine"], 0, 5) . "  " . $nom2['note'] . "<br>";
    }
    print "</td>";
    if ($_SESSION['tipoutente'] == 'T')
        print "<td align=center><a href='richiestaappuntamento.php?iddocente=" . $nom['iddocente'] . "&idclasse=$idclasse'><img src='../immagini/calendar.gif'></a></td>";
}





print"</tr>";
print "</table>";

print"<br/><center><a href=javascript:Popup('visdisponibilitastampa.php?idclasse=$idclasse')><img src='../immagini/stampa.png'></a><br/><br/>";





mysqli_close($con);
stampa_piede("");


