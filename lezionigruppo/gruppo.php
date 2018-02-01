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


$iddocente = stringa_html("iddocente");
$idmateria = stringa_html("idmateria");

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$destinatari = stringa_html('idgruppo');
$titolo = "Gestione gruppo";

$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


print ("
   <form method='post' action='gruppo.php' name='gruppo'>
   
   <table align='center'>");


print("   <tr>
      <td width='50%'><b>Docente</b></td>
      <td width='50%'>");
print ("<SELECT NAME='iddocente' ONCHANGE='gruppo.submit()'>");

$query = "SELECT * FROM tbl_docenti ORDER BY cognome, nome";
$ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
while ($rec = mysqli_fetch_array($ris))
{
    print "<option value='" . $rec['iddocente'] . "' ";
    if ($iddocente == $rec['iddocente'])
    {
        print "selected";
    }
    print ">" . estrai_dati_docente($rec['iddocente'], $con) . "</option>";
}

print "   
	   </SELECT>
      </td></tr>";

if ($iddocente != "")
{

    print("   <tr>
			<td width='50%'><b>Materia</b></td>
			<td width='50%'>");
    print ("<SELECT NAME='idmateria' ONCHANGE='gruppo.submit()'>");
    print ("<option value=''>&nbsp;</option>");
    $query = "select distinct tbl_materie.idmateria, denominazione from tbl_cattnosupp,tbl_materie
			  where tbl_cattnosupp.idmateria=tbl_materie.idmateria
			  and iddocente=$iddocente
			  and idalunno=0
			  order by denominazione";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
    $trovato = false;  // Gestisce la situazione di cambio docente
    while ($rec = mysqli_fetch_array($ris))
    {
        print "<option value='" . $rec['idmateria'] . "' ";
        if ($idmateria == $rec['idmateria'])
        {
            $trovato = true;
            print "selected";
        }
        print ">" . $rec['denominazione'] . "</option>";
    }
    if (!$trovato)     // Gestisce la situazione di cambio docente
    {
        $idmateria = '';
    }
    print "
			</SELECT>
			</td></tr>";

}

print "</table></form>";

if ($iddocente != "" && $idmateria != "")
{
    print "<form action='insgruppo.php' method='post'>";
    print "<input type='hidden' name='iddocente' value='$iddocente'>";
    print "<input type='hidden' name='idmateria' value='$idmateria'>";
    print "<br><table align='center' border='1'>";
    print("   <tr class='prima'>
			<td><b><center>Descrizione gruppo</b></center></td></tr><tr>
			<td><center>");
    print ("<input type='text' maxlength='255' size='50' name='descrizione'
	        value='" . estrai_dati_docente($iddocente, $con) . " - " . decodifica_materia($idmateria, $con) . " - '>");

    print"</center></td></tr>";


    print "</table>
	<center><br><input type='submit' value='Inserisci gruppo'></center></form>";
}


mysqli_close($con);
stampa_piede(""); 

