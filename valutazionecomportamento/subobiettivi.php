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
$tipoutente = $_SESSION["tipoutente"];
$iddocente = $_SESSION["idutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Gestione obiettivi del programma";
$script = "";
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


$maxabil = 10;
$maxcono = 10;

//$materia = stringa_html('materia');
//$anno = stringa_html('anno');
$idobiettivo = stringa_html('idobiettivo');

$idmateria = "";
$idclasse = "";

print ("
   <form method='post' action='subobiettivi.php' name='abilcono'>
   
   <p align='center'>
   <table align='center'>
");

print("<tr>
      <td width='50%'><p align='center'><b>Obiettivo</b></p></td>
      <td width='50%'>
      <SELECT ID='competenza' NAME='idobiettivo' ONCHANGE='abilcono.submit()'> <option value=''>&nbsp ");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT numeroordine, idobiettivo, obiettivo, sintob FROM tbl_compob
                  ORDER BY numeroordine";

// print inspref($query); // TTTT

$riscomp = mysqli_query($con, inspref($query));

while ($nom = mysqli_fetch_array($riscomp))
{
    print "<option value='";
    print ($nom["idobiettivo"]);
    print "'";
    if ($idobiettivo == $nom["idobiettivo"])
    {
        print " selected";
    }
    print ">";
    print ($nom["sintob"]);
    print "</option>";
}

//   }
print("
      </SELECT>
      </td></tr>");


print("</table><hr>");
print("</form>");
if ($idobiettivo != "")
{
    // Controllo presenza di voti per la programmazione della classe

    /*  $query="select count(*) as numerovoti from tbl_valutazioniobcomp, tbl_valutazionicomp, tbl_alunni
           where tbl_valutazioniobcomp.idvalint = tbl_valutazionicomp.idvalint
           and tbl_valutazionicomp.idalunno = tbl_alunni.idalunno
           and tbl_valutazionicomp.idmateria=$idmateria
           and tbl_alunni.idclasse=$idclasse
           and tbl_valutazionicomp.iddocente=$iddocente
           " ;
   */
    $query = "select * from tbl_valutazioniobcomp, tbl_compsubob,tbl_compob
              where 
                 tbl_valutazioniobcomp.idsubob = tbl_compsubob.idsubob
                 and tbl_compsubob.idobiettivo=tbl_compob.idobiettivo
                 and tbl_compob.idobiettivo=$idobiettivo";


    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . inspref($query));


    if (mysqli_num_rows($ris) > 0)
    {
        print ("<center><b><font color=red>Attenzione! Ci sono voti collegati a questa programmazione.<br/>
	            La modifica di alcune voci è quindi inibita!<br/>
	            Utilizzare la voce \"CORREGGI SUBOBIETTIVO\" per correzioni!</font></b></center>");
        // $votipresenti=true;
    }

//	  else
//	  {


    //
    //   GESTIONE ABILITA'
    //

    // print ("<table border=1 width='100%'><tr><td width='50%'>");

    $con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


    $query = "select * from tbl_compsubob where idobiettivo=$idobiettivo order by numeroordine";
    $ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
    print "<form method='post' action='inssubobiettivi.php'>
					  <p align='center'>
					 <font size=4 color='black'>Abilit&agrave;</font>
					 
					 <table border=1 align='center'>";
    $numord = 0;
    while ($val = mysqli_fetch_array($ris))
    {
        $numord++;
        $subobiettivo = $val["subob"];
        $sintsubob = $val["sintsubob"];
        $idsubob = $val["idsubob"];
        $votipresenti = false;
        $query = "select * from tbl_valutazioniobcomp
                      where idsubob=$idsubob";
        $ris2 = mysqli_query($con, inspref($query)) or die ("Errore: " . inspref($query));
        if (mysqli_num_rows($ris2) > 0)
        {
            $votipresenti = true;
        }


        if (!$votipresenti)
        {
            print "<tr><td align='center'>$numord</td><td align='center'>
							SINTESI: <input type=text name=sintab$numord value='$sintsubob' maxlength=80 size=80>
							<input type=hidden name=idabil$numord value='$idsubob'>";

            print"<br/><textarea rows=3 cols=80 name='ab$numord'>$subobiettivo</textarea></td></tr>";
        }
        else
        {
            print "<tr><td align='center'>$numord</td><td align='center'>
							SINTESI: <input type=text name=sintabdis$numord value='$sintsubob' maxlength=80 size=80 disabled>
							         <input type=hidden name=idabil$numord value='$idsubob'>
							         <input type='hidden'  name=sintab$numord value='$sintsubob'>";

            print"<br/><textarea rows=3 cols=80 name='abdis$numord' disabled>$subobiettivo</textarea>
					             <input type='hidden' name=ab$numord value='$subobiettivo'></td></tr>";
        }

    }
    for ($no = $numord + 1; $no <= $maxabil; $no++)
        print "<tr><td align='center'>$no</td><td align='center'>
							SINTESI: <input type=text name=sintab$no value='' maxlength=80 size=80><br/>
							<textarea rows=3 cols=80 name='ab$no'></textarea></td></tr>";
    print "</table></p>";



    print "<table align='center'>
						<tr><td colspan=2 align=center><input type='submit' value='Registra abilità e conoscenze'></tr></table>";
    print "<input type='hidden' name='idobiettivo' value='$idobiettivo'>";
    //print "<input type='hidden' name='anno' value='$anno'>";
    //print "<input type='hidden' name='materia' value='$materia'>";
    print "</form>";

}

else
{
    print("");
}


mysqli_close($con);
stampa_piede("");

