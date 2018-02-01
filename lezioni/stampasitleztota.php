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

$cogndoc = "";
$nomedoc = "";
$iddocente = $_SESSION["idutente"];
$titolo = "Situazione lezioni";

$lezperpag = 18;

// Stabilisco che dopo un testo di H1 (usato fittiziamente) si deve andare a pagina nuova

$script = "
	<script type='text/javascript'>
         <!--
            function printPage()
            {
               if (window.print)
                  window.print();
               else 
                  alert('Spiacente! il tuo browser non supporta la stampa diretta!');            }
         //-->
         </script>
        

";
//<style> 
// @media print
//          {
//              h1 {page-break-after:always}
//           }
//</style>

stampa_head($titolo, "", $script, "SDMAP");

print ('<body class="stampa" onLoad="printPage()">');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


$per = stringa_html('periodo');
$catt = stringa_html('cattedra');


// Prelevo classe e materia dalla cattedra selezionata
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));


/*
if ($catt<>"")
{
   $query="select idclasse, idmateria from tbl_cattnosupp where idcattedra=$catt"; 
   $ris=mysqli_query($con,inspref($query));
   if($nom=mysqli_fetch_array($ris))
    {
        $mat=$nom['idmateria'];
        $cla=$nom['idclasse'];
    }
}
*/

$idclasse = estrai_id_classe($catt, $con);
$idmateria = estrai_id_materia($catt, $con);
$idgruppo = "";
$query = "select distinct tbl_gruppi.idgruppo from tbl_gruppialunni,tbl_alunni,tbl_gruppi
           where tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
             and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
             and tbl_alunni.idclasse=$idclasse
             and tbl_gruppi.idmateria=$idmateria
             and tbl_gruppi.iddocente=$iddocente";
$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
if ($rec = mysqli_fetch_array($ris))
{
    $idgruppo = $rec['idgruppo'];
}


$id_ut_doc = $_SESSION["idutente"];


// Divido il mese dall'anno
//$mese=substr($meseanno,0,2);
//$anno=substr($meseanno,5,4);


// Estraggo nome della classe

$query = 'SELECT * FROM tbl_classi WHERE idclasse="' . $idclasse . '" ';
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
if ($val = mysqli_fetch_array($ris))
{
    $classe = $val["anno"] . " " . $val["sezione"] . " " . $val["specializzazione"];
}


// Estraggo nome della materia

$query = 'SELECT * FROM tbl_materie WHERE idmateria="' . $idmateria . '" ';
$ris = mysqli_query($con, inspref($query)) or die ("Errore nella query: " . mysqli_error($con));
if ($val = mysqli_fetch_array($ris))
{
    $nomemateria = $val["denominazione"];
}

// Estraggo il nominativo del docente
$query = "select iddocente, cognome, nome from tbl_docenti where idutente=$id_ut_doc";

$ris = mysqli_query($con, inspref($query));
if ($nom = mysqli_fetch_array($ris))
{

    $cogndoc = $nom["cognome"];
    $nomedoc = $nom["nome"];

}


//
//   ESTRAZIONE DATI DELLE LEZIONI
//  

$giornilezione = array();
$perioquery = "and true";
if ($per == "Primo")
{
    $perioquery = " and datalezione <= '" . $fineprimo . "'";
}
if ($per == "Secondo" & $numeroperiodi == 2)
{
    $perioquery = " and datalezione > '" . $fineprimo . "'";
}
if ($per == "Secondo" & $numeroperiodi == 3)
{
    $perioquery = " and datalezione > '" . $fineprimo . "' and datalezione <=  '" . $finesecondo . "'";
}
if ($per == "Terzo")
{
    $perioquery = " and datalezione > '" . $finesecondo . "'";
}


$query = "SELECT sum(numeroore) AS numtotore FROM tbl_lezioni WHERE idclasse='" . $idclasse . "' AND idmateria='" . $idmateria . "' " . $perioquery;
$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
if ($val = mysqli_fetch_array($ris))
{
    $oretotalilezione = $val["numtotore"];
}


$query = "SELECT idlezione,datalezione, numeroore,orainizio FROM tbl_lezioni WHERE idclasse='" . $idclasse . "' AND idmateria='" . $idmateria . "' " . $perioquery . " ORDER BY datalezione";

$rislez = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));

while ($reclez = mysqli_fetch_array($rislez))

{
    $gio = substr($reclez['datalezione'], 8, 2);
    $mes = substr($reclez['datalezione'], 5, 2);
    $ann = substr($reclez['datalezione'], 0, 4);
    $gio = $ann . $mes . $gio . $reclez['orainizio'] . $reclez['datalezione'] . $reclez['numeroore'] . $reclez['idlezione'];
    $giornilezione[] = $gio;

}


// Contiene il numero di giorni di lezione da stampare
$numerotbl_lezioni = count($giornilezione);


$perioquery = "and true";
if ($per == "Primo")
{
    $perioquery = " and data <= '" . $fineprimo . "'";
}
if ($per == "Secondo" & $numeroperiodi == 2)
{
    $perioquery = " and data > '" . $fineprimo . "'";
}
if ($per == "Secondo" & $numeroperiodi == 3)
{
    $perioquery = " and data > '" . $fineprimo . "' and data <=  '" . $finesecondo . "'";
}
if ($per == "Terzo")
{
    $perioquery = " and data > '" . $finesecondo . "'";
}


// Estraggo tutte le valutazioni registrate per la classe, materia e periodo in esame per aggiungerle alle tbl_lezioni se non sono state registrate
/*    $query="select distinct data from tbl_valutazioniintermedie,tbl_alunni where tbl_valutazioniintermedie.idalunno=tbl_alunni.idalunno and idclasse='".$idclasse."' and         idmateria='".$idmateria."' ".$perioquery." order by data";
    
    $risvot=mysqli_query($con,inspref($query)) or die ("Errore nella query: ". mysqli_error($con));
    
    while ($recvot=mysqli_fetch_array($risvot))
  
    {
       $presente=false;
       foreach($giornilezione as $ggia)
       {
          if (substr($ggia,6,2)==substr($recvot['data'],8,2) & substr($ggia,4,2)==substr($recvot['data'],5,2))
             $presente=true;
       }
       if (!$presente)
       { 
          $gio=substr($recvot['data'],8,2);
          $mes=substr($reclez['data'],5,2);
	  $ann=substr($reclez['data'],0,4);
          $gio=$ann.$mes.$gio.$recvot['data']."0"."00000000000"; 
          $giornilezione[]=$gio;
          $numerotbl_lezioni++;  // incremento il contatore dei numeri di giorni da visualizzare tttttttt
       } 
      
    } 
*/
sort($giornilezione);
// print("tbl_lezioni $numerotbl_lezioni");

//
//    INIZIO STAMPA
//

if ($_SESSION['suffisso'] != "")
{
    $suff = $_SESSION['suffisso'] . "/";
}
else $suff = "";
print ("<center><img src='../abc/" . $suff . "testata.jpg' width='600'></center>");

$annoscolastico = $annoscol . "/" . ($annoscol + 1);


// TTTTTTT     Far stampare una tabella di massimo 18 tbl_lezioni o salto a fine mese

$num = ($numerotbl_lezioni + 5) % $lezperpag; // Aggiungo 5 per tenere conto delle proposte di voto

$numeropagine = (int)(($numerotbl_lezioni + 5) / $lezperpag);

if ($num > 0) $numeropagine++;
// print ("num $num numero $numeropagine");


for ($np = 1; $np <= $numeropagine; $np++)
{
    print ("<font size=2><center><br/>A.S. <i>$annoscolastico</i> <br/>Lezioni della classe <i>$classe</i> - Materia: <i>$nomemateria</i> - <i>$per");
    if ($numeroperiodi == 2)
    {
        print " Quadrimestre</i>";
    }
    else
    {
        print " Trimestre</i>";
    }
    if ($np == 1) print ("<br/>Totale ore di lezione: <i>$oretotalilezione</i><br/>");
    print ("<center><br/>Pag. $np/$numeropagine<br/><br/></center>");

    echo '<table class="smallchar" border=1 align="center">';

    if ($np == 1)
    {
        echo '<tr><td rowspan=2><b><center> Alunno </center></b></td>';
    }
    else
    {
        echo '<tr><td><b><center> Alunno </center></b></td>';
    }
    // stampo l'intestazione delle proposte solo sulla prima pagina 
    if ($np == 1)
    {
        print "<td colspan=5><center><b>Proposte di voto</b></td>";

    }

    $numeroalunno = 0;

    $limite = 0;
    // print ("uno$limite");
    if ($np < $numeropagine | $num == 0)
    {
        $limite = $lezperpag;
    }
    else $limite = $num;
    // print  ("due$limite");
    if ($np == 1) $limite = $limite - 5;
    // print ("tre$limite");
    for ($ng = 0; $ng < $limite; $ng++)
    {
        if ($np > 1)
        {
            $gg = $giornilezione[($np - 2) * $lezperpag + ($lezperpag - 5) + $ng];
        }  //Modifica per tenere conto delle proposte di voto
        else
        {
            $gg = $giornilezione[$ng];
        }


        if ($np == 1)
        {
            $strore = substr($gg, 8, 1) . ">" . ((substr($gg, 19, 1) + substr($gg, 8, 1) - 1));
            print "<td rowspan=2><b><center>" . giorno_settimana(substr($gg, 9, 10)) . "<br/>" . substr($gg, 6, 2) . "<br/>" . substr($gg, 4, 2) . "<br/>$strore</a></td>";
            //    if (substr($gg,12,1)!="0")
            //       print "<td rowspan=2><b><center>".giorno_settimana(substr($gg,8,10))."<br/>".substr($gg,6,2)."<br/>".substr($gg,4,2)."<br/>".substr($gg,18,1)."</b></td>";
            //    else
            //       print "<td rowspan=2><b><center>".giorno_settimana(substr($gg,2,10))."<br/>".substr($gg,0,2)."<br/>".substr($gg,2,2)."<br/>".substr($gg,12,1)."</b></td>";
        }
        else
        {
            $strore = substr($gg, 8, 1) . ">" . ((substr($gg, 19, 1) + substr($gg, 8, 1) - 1));
            print "<td><b><center>" . giorno_settimana(substr($gg, 9, 10)) . "<br/>" . substr($gg, 6, 2) . "<br/>" . substr($gg, 4, 2) . "<br/>$strore</a></td>";
            //    if (substr($gg,12,1)!="0")
            //       print "<td><b><center>".giorno_settimana(substr($gg,8,10))."<br/>".substr($gg,6,2)."<br/>".substr($gg,4,2)."<br/>".substr($gg,18,1)."</b></td>";
            //    else
            //       print "<td><b><center>".giorno_settimana(substr($gg,2,10))."<br/>".substr($gg,0,2)."<br/>".substr($gg,2,2)."<br/>".substr($gg,12,1)."</b></td>";
        }

    }


    if ($np == $numeropagine)
    {
        if ($np == 1)
        {
            print "<td rowspan=2 align='center' valign='middle'><b>Ass.<br/>tot.</b></td>";
        }
        else
        {
            print "<td align='center' valign='middle'><b>Ass.<br/>tot.</b></td>";
        }
    }
    print "</tr>";


    if ($np == 1)
    {
        print "<tr>
           <td><font size=1><b>Sc</b></td>
          <td><font size=1><b>Or</b></td>
          <td><font size=1><b>Pr</b></td>
          <td><font size=1><b>Un</b></td>
          <td><font size=1><b>Co</b></td></tr> ";

    }

    $query = 'SELECT * FROM tbl_alunni WHERE idclasse="' . $idclasse . '" ORDER BY cognome,nome,datanascita';
    if ($idgruppo == '')
    {
        $query = "select * from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";
    }
    else
    {
        $query = "select tbl_alunni.idalunno,cognome,nome,datanascita
                  from tbl_gruppi,tbl_gruppialunni,tbl_alunni
                  where
                       tbl_gruppi.idgruppo=tbl_gruppialunni.idgruppo
                       and tbl_gruppialunni.idalunno=tbl_alunni.idalunno
                       and tbl_alunni.idclasse=$idclasse
                       and tbl_gruppi.idgruppo in (select idgruppo from tbl_gruppi where idmateria=$idmateria and iddocente=$iddocente)
                       order by cognome,nome,datanascita";
    }


    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
    while ($val = mysqli_fetch_array($ris))
    {
        echo '
             <tr>
                <td>' . $val["cognome"] . ' ' . $val["nome"] . ' (' . data_italiana($val["datanascita"]) . ')' . '</td>
                ';

        $numeroalunno++;
        if ($np == 1)
        {
            $totoreass[$numeroalunno] = 0;
        }

// Codice per stampa tbl_proposte di voto

        if ($np == 1)
        {
            if ($per == "Primo") $perio = '1';
            if ($per == "Secondo") $perio = '2';
            if ($per == "Terzo") $perio = '3';
            $queryprop = 'SELECT * FROM tbl_proposte WHERE idalunno=' . $val['idalunno'] . ' AND idmateria =' . $idmateria . ' AND periodo="' . $perio . '"';

            $risprop = mysqli_query($con, inspref($queryprop)) or die("Errore: " . inspref($queryprop));
            if ($valprop = mysqli_fetch_array($risprop))
            {

                print "<td><font size=1><center><b>" . dec_to_vot($valprop['scritto']) . "</b></td>";
                print "<td><font size=1><center><b>" . dec_to_vot($valprop['orale']) . "</b></td>";
                print "<td><font size=1><center><b>" . dec_to_vot($valprop['pratico']) . "</b></td>";
                print "<td><font size=1><center><b>" . dec_to_vot($valprop['unico']) . "</b></td>";
                print "<td><font size=1><center><b>" . dec_to_vot($valprop['condotta']) . "</b></td>";
            }
            else
            {
                print "<td><b>&nbsp;</b></td>";
                print "<td><b>&nbsp;</b></td>";
                print "<td><b>&nbsp;</b></td>";
                print "<td><b>&nbsp;</b></td>";
                print "<td><b>&nbsp;</b></td>";
            }

        }


        // Codice per stampa dati sugli tbl_alunni

        for ($ng = 0; $ng < $limite; $ng++)
        {

            // $gg = $giornilezione[($np-1)*$lezperpag+$ng];
            if ($np > 1)
            {
                $gg = $giornilezione[($np - 2) * $lezperpag + ($lezperpag - 5) + $ng];
            }  //Modifica per tenere conto delle proposte di voto
            else
            {
                $gg = $giornilezione[$ng];
            }

            //   foreach($giornilezione as $gg)
            //   {

            print "<td align='center'>";


            // RICERCA ORE DI ASSENZA

            $query = "SELECT oreassenza FROM tbl_asslezione WHERE idalunno=" . $val['idalunno'] . " AND idlezione='" . substr($gg, 20, 11) . "'";

            $risass = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
            if (mysqli_num_rows($risass) > 0)
            {
                $ass = mysqli_fetch_array($risass);
                print "A<sub>" . $ass['oreassenza'] . "</sub>";
                $totoreass[$numeroalunno] = $totoreass[$numeroalunno] + $ass['oreassenza'];
            }


            // RICERCA VALUTAZIONI

            $query = "SELECT voto, giudizio, tipo FROM tbl_valutazioniintermedie WHERE idalunno=" . $val['idalunno'] . " AND idlezione='" . substr($gg, 20, 11) . "'";

            $risvot = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query));
            if (mysqli_num_rows($risvot) > 0)
            {
                while ($vot = mysqli_fetch_array($risvot))
                    if ($vot['voto'] >= 6)
                    {
                        print "&nbsp;" . dec_to_mod($vot['voto']) . "<sub>" . $vot['tipo'] . "</sub>";
                    }
                    else
                    {
                        print "&nbsp;" . dec_to_mod($vot['voto']) . "<sub>" . $vot['tipo'] . "</sub>";
                    }
            }


            print "&nbsp;</td>";
        }


        if ($np == $numeropagine)
        {
            print("<td align='center'>$totoreass[$numeroalunno]</td>");
        }
        // Fine codice per ricerca tbl_assenze gi� inserite

        print"</tr>";

    }

    echo '</table>';
    // Salto pagina se non � l'ultima

    if ($np < $numeropagine)
    {
        print("<h1>&nbsp;</h1>");
    }
    else
    {
        print("<br/><br/><table border=0 width=100%><tr><td width=50%>&nbsp</td><td width=50% align='center'>Il docente<br/>(Prof. $nomedoc $cogndoc)<br/><br/>______________________________</td></tr></table>");
    }


}


// fine if


mysqli_close($con);


