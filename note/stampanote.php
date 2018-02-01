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

 @require_once("../php-ini".$_SESSION['suffisso'].".php");
 @require_once("../lib/funzioni.php");

 // istruzioni per tornare alla pagina di login se non c'� una sessione valida
 ////session_start();
 $tipoutente=$_SESSION["tipoutente"]; //prende la variabile presente nella sessione
    if ($tipoutente=="")
       {
	   header("location: ../login/login.php?suffisso=".$_SESSION['suffisso']);
	   die;
       }



$titolo="Stampa note";
$script="";
$idclasse=stringa_html('classe');
$periodo=stringa_html('periodo');
stampa_head($titolo,"",$script,"SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo","","$nome_scuola","$comune_scuola");

print ("
   <form method='post' action='stampanote.php' name='note'>
   
   <p align='center'>
   <table align='center'>
   <tr>
      <td width='50%'><p align='center'><b>Classe</b></p></td>
      <td width='50%'>
      <SELECT ID='classe' NAME='classe' onchange='note.submit()'>
      <option value=''>&nbsp;  ");



$con=mysqli_connect($db_server,$db_user,$db_password,$db_nome) or die ("Errore durante la connessione: ".mysqli_error($con));


// Riempimento combo box tbl_classi
if ($tipoutente=="S" | $tipoutente=="P")
   $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi order by anno,sezione,specializzazione";
else
   $query="select distinct tbl_classi.idclasse,anno,sezione,specializzazione from tbl_classi
           where idcoordinatore=".$_SESSION['idutente']. " order by anno,sezione,specializzazione";
$ris=mysqli_query($con,inspref($query));
while($nom=mysqli_fetch_array($ris))
{

   print "<option value='";
   print ($nom["idclasse"]);
   print "'";
   if ($nom["idclasse"]==$idclasse) print " selected";
   print ">";
   print ($nom["anno"]);
   print "&nbsp;";
   print($nom["sezione"]);
   print "&nbsp;";
   print($nom["specializzazione"]);
}

echo('
      </SELECT>
      </td></tr>');

//
//   Inizio visualizzazione del combo box del periodo
//
if ($numeroperiodi==2)
   print("<tr><td width='50%'><b>Quadrimestre</b></td>");
else
   print("<tr><td width='50%'><b>Trimestre</b></td>");

echo("   <td width='50%'>");
if ($periodo=="Primo") $selpr=" selected"; else $selpr="";
if ($periodo=="Secondo") $selse=" selected"; else $selse="";
if ($periodo=="Terzo") $selte=" selected"; else $selte="";
if ($periodo=="Tutti") $seltu=" selected"; else $seltu="";
echo("   <select name='periodo' onchange='note.submit()'>");


  echo("<option$selpr>Primo</option>");
  echo("<option$selse>Secondo</option>");

if ($numeroperiodi==3)
     echo("<option$selte>Terzo</option>");

  echo("<option$seltu>Tutti</option>");


echo("</select>");
echo("</td></tr>");

echo("</table>");
print ("</form>");
if ($idclasse!="")
{

 //
//  VISUALIZZO I DATI DELLA CLASSE 
//

print ("");

$query = "select * from tbl_classi where idclasse=$idclasse";
$ris=mysqli_query($con,inspref($query)) or die ("Errore: ".inspref($query));
$cla=mysqli_fetch_array($ris);

print ("<center><b><br>Note&nbsp;della&nbsp;classe&nbsp;".$cla['anno']."&nbsp;".$cla['sezione']."&nbsp;".$cla['specializzazione']."<br/><br/>");

//
//  VISUALIZZO LE NOTE DI CLASSE
//


 if ($periodo=="Tutti")
    $query="select data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
            from tbl_noteclasse, tbl_docenti 
            where tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and idclasse = $idclasse 
            order by tbl_noteclasse.data";

 if ($periodo=="Primo")
    $query="select data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
            from tbl_noteclasse, tbl_docenti 
            where tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and idclasse = $idclasse  and data <= '".$fineprimo."'
            order by tbl_noteclasse.data";

 if ($periodo=="Secondo" & $numeroperiodi==2)
    $query="select data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
            from tbl_noteclasse, tbl_docenti 
            where tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and idclasse = $idclasse  and data > '".$fineprimo."'
            order by tbl_noteclasse.data";

 if ($periodo=="Secondo"  & $numeroperiodi==3 )
    $query="select data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
            from tbl_noteclasse, tbl_docenti 
            where tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and idclasse = $idclasse and  data >  '".$fineprimo."' and data <=  '".$finesecondo."'
            order by tbl_noteclasse.data";

 if ($periodo=="Terzo")
    $query="select data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
            from tbl_noteclasse, tbl_docenti 
            where tbl_noteclasse.iddocente=tbl_docenti.iddocente  
            and idclasse = $idclasse  and data > '".$finesecondo."'
            order by tbl_noteclasse.data";






 $ris=mysqli_query($con,inspref($query));


 $c=mysqli_num_rows($ris);


    if ($c==0)
    {
	   echo "<center><b>NOTE DI CLASSE</b></center><br/>";
       echo "<center><b>Nessuna nota di classe!</b></center><br/>";
    }
    else
    {
       echo "<center><b>NOTE DI CLASSE</b></center><br/>";
       print "<table border=1 width=95%>";
       while ($rec=mysqli_fetch_array($ris))
       {
           print "<tr class='prima'><td colspan=2><small><center><b> Nota del docente&nbsp;".$rec['cogndocente']."&nbsp;".$rec['nomedocente']."&nbsp;in&nbsp;data&nbsp;".data_italiana($rec['data'])."</td></tr>";
           print("<tr>");

           print("<td width=50%><small>");
           print("".$rec['testo']."");
           print("</td>");
           print("<td width=50%><small>");
           print("".$rec['provvedimenti']."");
           print("</td></tr>");


       }
       print "</table>";
    }

//
// VISUALIZZO LE NOTE INDIVIDUALI




     if ($periodo=="Primo")
        $query="select idnotaalunno, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
                from tbl_notealunno, tbl_docenti 
                where tbl_notealunno.iddocente=tbl_docenti.iddocente
                and tbl_notealunno.idclasse = $idclasse and data <= '".$fineprimo."'
                order by data,idnotaalunno";

    if ($periodo=="Secondo" & $numeroperiodi==2)
        $query="select idnotaalunno, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
                from tbl_notealunno, tbl_docenti 
                where tbl_notealunno.iddocente=tbl_docenti.iddocente
                and tbl_notealunno.idclasse = $idclasse and data > '".$fineprimo."'
                order by data,idnotaalunno";

    if ($periodo=="Secondo"  & $numeroperiodi==3 )
        $query="select idnotaalunno, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
                from tbl_notealunno, tbl_docenti 
                where tbl_notealunno.iddocente=tbl_docenti.iddocente
                and tbl_notealunno.idclasse = $idclasse and  data >  '".$fineprimo."' and data <=  '".$finesecondo."'
                order by data,idnotaalunno";
    if ($periodo=="Terzo")
        $query="select idnotaalunno, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
                from tbl_notealunno, tbl_docenti 
                where tbl_notealunno.iddocente=tbl_docenti.iddocente
                and tbl_notealunno.idclasse = $idclasse and data > '".$finesecondo."'
                order by data,idnotaalunno";
    if ($periodo=="Tutti")
        $query="select idnotaalunno, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
                from tbl_notealunno, tbl_docenti 
                where tbl_notealunno.iddocente=tbl_docenti.iddocente
                and tbl_notealunno.idclasse = $idclasse
                order by data,idnotaalunno";


    $ris=mysqli_query($con,inspref($query));


    $c=mysqli_num_rows($ris);


    if ($c==0)
    {
	   echo "<center><b><br/>NOTE INDIVIDUALI</b></center><br/>";
       echo "<center><b><br/>Nessuna nota individuale!</b></center><br/>";
    }
    else
    {
		echo "<center><b><br/>NOTE INDIVIDUALI</b></center><br/>";
       print "<br><table border=1 width=95%>";


       while ($rec=mysqli_fetch_array($ris))
       {

              print "<tr class='prima'><td colspan=2><center><small><b>Nota del docente&nbsp;".$rec['cogndocente']."&nbsp;".$rec['nomedocente']."&nbsp;in data&nbsp;".data_italiana($rec['data']);

              $queryalu="select tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno                  from tbl_noteindalu, tbl_alunni
                where tbl_noteindalu.idnotaalunno=".$rec['idnotaalunno'].
                " and tbl_noteindalu.idalunno=tbl_alunni.idalunno";
              $risalu=mysqli_query($con,inspref($queryalu)) or die ("Errore: ".inspref($queryalu));
              $elencoalunni="";
              if (mysqli_num_rows($risalu)>1)
                  $elencoalunni="Alunni: ";
              else
                  $elencoalunni="Alunno: ";
              while ($recalu=mysqli_fetch_array($risalu))
              {
				  $elencoalunni.=$recalu['cognalunno']."&nbsp;".$recalu['nomealunno']."&nbsp;(".data_italiana($recalu['dataalunno'])."), ";
			  }
              $elencoalunni=substr($elencoalunni,0,strlen($elencoalunni)-2);  // Elimino la virgola finale
              print ("<br/>$elencoalunni</td></tr>");
              print ("<tr>");

           print("<td width=50%><small>");
           print("".$rec['testo']."");
           print("</td>");
           print("<td width=50%><small>");
           print("".$rec['provvedimenti']."");
           print("</td></tr>");


       }
       print "</table>";
    }

    print "
    <form method='post' action='stanote.php' target='_blank' name='stanote'>
    <table align='center'>
      <td>
         <input type='hidden' name='classe' value='$idclasse'>
         <input type='hidden' name='periodo' value='$periodo'>
        <p align='center'><input type='submit' value='Stampa' name='b' onclick='Popup(stanote.php)'></p>
     </form></td>
   
</table><hr>
 
    ";

}

mysqli_close($con);
stampa_piede(""); 

