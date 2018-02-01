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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Evacuazione";
$script = "";
stampa_head($titolo, "", $script,"DS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die ("Errore durante la connessione: " . mysqli_error($con));

$giorno = '';
$meseanno = '';
$anno = '';
$mese = '';
$idclasse = '';

// MODIFICA ANTE TOKEN
$iddocente = $_SESSION['idutente'];
// FINE MODIFICA ANTE TOKEN


$giorno = stringa_html('gio');
//$orainizio = stringa_html('orainizio');
$meseanno = stringa_html('meseanno');
$idclasse = stringa_html('idclasse');
$den_classe = '';
$aula = stringa_html('aula');
$alunniassenti = stringa_html('alunniassenti');
$alunninoninaula = stringa_html('alunninoninaula');
$luogononinaula = stringa_html('luogononinaula');
$alunnievacuati = stringa_html('alunnievacuati');
$numaltrepersone = stringa_html('numaltrepersone');
$altrepersone = stringa_html('altrepersone');
$aprifila = stringa_html('aprifila');
$chiudifila = stringa_html('chiudifila');
$zonaraccolta = stringa_html('zonaraccolta');
$tipoemergenza = stringa_html('tipoemergenza');
$insegnante = stringa_html('insegnante');

// Le variabili di sessione servono agli altri programmmi a stabilire che si proviene
// dal registro per poter fare automaticamente ritorno qui.

$_SESSION['prove'] = 'evacuazione.php';
$_SESSION['regcl'] = $idclasse;
$_SESSION['regma'] = $meseanno;
$_SESSION['reggi'] = $giorno;

$_SESSION['classeregistro'] = $idclasse;

$anno = substr($meseanno, 5, 4);
$mese = substr($meseanno, 0, 2);

$giornosettimana = "";

/*
if ($idlezione!="" & $giorno!="" & $meseanno!="" )
{
	 $mese=substr($meseanno,0,2);
    $anno=substr($meseanno,5,4);

     //  $giornosettimana=giorno_settimana($anno."-".$mese."-".$giorno);

    $query="select idclasse, idmateria from tbl_cattnosupp where idcattedra=$cattedra";

       $ris=mysqli_query($con,inspref($query));
       if($nom=mysqli_fetch_array($ris))
       {
           $materia=$nom['idmateria'];
           $idclasse=$nom['idclasse'];
       }


   }
}
*/


if ($giorno == '')
{
    $giorno = date('d');
}
if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')
{
    $anno = date('Y');
}

print ('
         <form method="post" action="evacuazione.php" name="evacuazione">

         <p align="center">
         <table align="center">');

print ('         <tr>
         <td width="50%"><b>Data (gg/mm/aaaa)</b></td>');


//
//   Inizio visualizzazione della data
//


echo('   <td width="50%">');
echo('   <select name="gio" ONCHANGE="evacuazione.submit()">');
for ($g = 1; $g <= 31; $g++)
{
    if ($g < 10)
    {
        $gs = '0' . $g;
    }
    else
    {
        $gs = '' . $g;
    }
    if ($gs == $giorno)
    {
        echo("<option selected>$gs</option>");
    }
    else
    {
        echo("<option>$gs</option>");
    }
}
echo("</select>");

echo('   <select name="meseanno" ONCHANGE="evacuazione.submit()">');
for ($m = 9; $m <= 12; $m++)
{
    if ($m < 10)
    {
        $ms = "0" . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        echo("<option selected>$ms - $annoscol</option>");
    }
    else
    {
        echo("<option>$ms - $annoscol</option>");
    }
}
$annoscolsucc = $annoscol + 1;
for ($m = 1; $m <= 8; $m++)
{
    if ($m < 10)
    {
        $ms = '0' . $m;
    }
    else
    {
        $ms = '' . $m;
    }
    if ($ms == $mese)
    {
        echo("<option selected>$ms - $annoscolsucc</option>");
    }
    else
    {
        echo("<option>$ms - $annoscolsucc</option>");
    }
}
echo("</select>");


/*
    echo('   <select name="anno">');
    for($a=$annoscol;$a<=($annoscol+1);$a++)
    {
      if ($a==$anno)
         echo("<option selected>$a");
      else
         echo("<option>$a");
    }
    echo("</select>");
*/
//
//  Fine visualizzazione della data
//


echo("
      </td></tr>");


//
//   Classi
//
print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%">
        <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="evacuazione.submit()"><option value="">&nbsp;');

//
//  Riempimento combobox delle classi
//

// $query="select idclasse, anno, sezione, specializzazione from tbl_classi order by anno, sezione, specializzazione";
$query = "select idclasse, anno, sezione, specializzazione from tbl_classi
        order by anno, sezione, specializzazione";


$ris = mysqli_query($con, inspref($query));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
//  if ($cattedra==$nom["idcattedra"])
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    if ($idclasse == $nom["idclasse"])
    {
        $den_classe.=$nom["anno"];
        $den_classe.=" ";
    }
    print "&nbsp;";
    print($nom["sezione"]);
    if ($idclasse == $nom["idclasse"])
    {
        $den_classe.=$nom["sezione"];
        $den_classe.=" ";
    }
    print "&nbsp;";
    print($nom["specializzazione"]);
    if ($idclasse == $nom["idclasse"])
    {
        $den_classe.=$nom["specializzazione"];
    }
    print "</option>";
}

echo('
      </SELECT>
      </td></tr>');

echo('<tr><td><input type="hidden" name="denominazioneclasse" value="' . $den_classe . '"</tr></td>');

$dataoggi = "$anno-$mese-$giorno";
$datadomani = aggiungi_giorni($dataoggi, 1);
$dataieri = aggiungi_giorni($dataoggi, -1);
if (giorno_settimana($dataieri) == "Dom") $dataieri = aggiungi_giorni($dataieri, -1);
if (giorno_settimana($datadomani) == "Dom") $datadomani = aggiungi_giorni($datadomani, +1);
$gioieri = substr($dataieri, 8, 2);
$giodomani = substr($datadomani, 8, 2);
$maieri = substr($dataieri, 5, 2) . " - " . substr($dataieri, 0, 4);
$madomani = substr($datadomani, 5, 2) . " - " . substr($datadomani, 0, 4);


if ($mese == "")
{
    $m = 0;
}
else
{
    $m = $mese;
}
if ($giorno == "")
{
    $g = 0;
}
else
{
    $g = $giorno;
}

if ($anno == "")
{
    $a = 0;
}
else
{
    $a = $anno;
}

$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);
//if ($cattedra!="")
//{
//   $query="select * from tbl_cattnosupp where iddocente='$iddocente' and idclasse='$idclasse' and idmateria='$materia'";
//   // print inspref($query);
//   $ris=mysqli_query($con,inspref($query));
//   $numerorighe=mysqli_num_rows($ris);
//}

echo('<tr><td><input type="hidden" name="dataformattata" value="' . $g . "/" . $m . "/" . $a . '"</td></tr>');

if (!checkdate($m, $g, $a))
{
    echo('</table>');
    echo('</br>');
    print ("<Center> <big><big>Data non corretta!</big></big> </center>");
}
else
{
    if ($giornosettimana == "Dom")
    {
        echo('</table>');
        echo('</br>');
        print ("<Center> <big><big>Il giorno selezionato &egrave; una domenica!</big></big> </center>");
    }
// else if (($anno.$mese.$giorno)>date("Ymd"))
//   print ("<Center> <big><big>Data selezionata maggiore della data odierna!</big></big> </center>");
    else
    {
      // COMPILAZIONE FORM

      $query = "SELECT * FROM tbl_alunni,tbl_utenti
       WHERE tbl_alunni.idalunno=tbl_utenti.idutente
       AND idclasse='$idclasse'ORDER BY cognome,nome";

      $ris = mysqli_query($con, inspref($query));
      $num_alunni = mysqli_num_rows($ris);
      echo('<tr><td><input type="hidden" name="numeroalunni" value="' . $num_alunni . '"</tr></td>');
      if(!$idclasse)
      {
        $num_alunni = 0;
      }
      else
      {
      print('<tr><td></br></td></tr>');
      print('</table>');

      // ELENCO ALUNNI PER APPELLO
      print('<center><b><big>ELENCO ALUNNI</big></b></center></br>');
      print('<table align="center" width="40%" border="1">');
      print("<tr><td align='center'><b> N.</b> </td>");
      print("<td align='center'><b> Cognome</b> </td>");
      print("<td align='center'><b> Nome</b> </td></tr>");
      $contatore = 0;
      while($dati = mysqli_fetch_array($ris))
      {
        $contatore++;
        print('<tr>
        <td width="10%">');
        print($contatore);
        print('</td>
        <td width="45%">');
        print($dati['cognome']);
        print('</td>
        <td width="45%">');
        print($dati['nome']);
        print('</td>');
      }
      print('</table></br>');

      // TEXT BOX AULA
      print('<p align="center">
      <table align="center">');
      print('
              <tr>
              <td width="50%"><b>Aula occupata al momento dell\'evacuazione: </b></td>
              <td width="50%">');
      print('<input type="text" name="aula" size="25" ONCHANGE="evacuazione.submit()" value="');
      print("$aula");
      print('"></td></tr>');

      // COMBO BOX ASSENTI
      print('
              <tr>
              <td width="50%"><b>Numero alunni assenti: </b></td>
              <td width="50%">
              <SELECT ID="alunniassenti" NAME="alunniassenti" ONCHANGE="evacuazione.submit()">&nbsp;');
              for($n=0; $n<=$num_alunni; $n++)
              {
                  print "<option value='";
                  print "$n";
                  print "'";
                  if ($alunniassenti == $n)
                  {
                      print " selected";
                  }
                  print(">$n");
                  print "</option>";
              }
              echo('</SELECT></td></tr>');

      // COMBO BOX NON IN AULA
      print('
              <tr>
              <td width="50%"><b>Numero alunni presenti ma non in aula: </b></td>
              <td width="50%">
              <SELECT ID="alunninoninaula" NAME="alunninoninaula" ONCHANGE="evacuazione.submit()">&nbsp;');
              for($n=0; $n<=$num_alunni-$alunniassenti; $n++)
              {
                  print "<option value='";
                  print "$n";
                  print "'";
                  if ($alunninoninaula == $n)
                  {
                      print " selected";
                  }
                  print(">$n");
                  print "</option>";
              }
              echo('</SELECT>');

      // INDICARE LUOGO
      print('<b>&nbsp;&nbsp;&nbsp; Indicare luogo: </b><input type="text" name="luogononinaula" size="15" ONCHANGE="evacuazione.submit()" value="');
      print("$luogononinaula");
      print('"></td></tr>');

      // COMBO BOX EVACUATI
      print('
              <tr>
              <td width="50%"><b>Numero alunni evacuati: </b></td>
              <td width="50%">
              <SELECT ID="alunnievacuati" NAME="alunnievacuati" ONCHANGE="evacuazione.submit()">&nbsp;');
              for($n=0; $n<=$num_alunni-$alunniassenti; $n++)
              {
                  print "<option value='";
                  print "$n";
                  print "'";
                  if ($alunnievacuati == $n)
                  {
                      print " selected";
                  }
                  print(">$n");
                  print "</option>";
              }
              echo('</SELECT></td></tr>');

      // TEXT BOX ALTRE PERSONE
      print('
              <tr>
              <td width="50%"><b>Numero altre persone presenti: </b></td>
              <td width="50%">');
      print('<input type="text" name="numaltrepersone" ONCHANGE="evacuazione.submit()" size="5" value="');
      print("$numaltrepersone");
      print('"></td></tr>');

      // INDICARE ALTRE PERSONE
      if($numaltrepersone && $numaltrepersone>0)
      {
      print('
              <tr>
              <td width="50%"><b>Indicare altre persone presenti: </b></td>
              <td width="50%">');
      print('<input type="text" name="altrepersone" size="20" ONCHANGE="evacuazione.submit()" value="');
      print("$altrepersone");
      print('"></td></tr>');
    }

      print('<tr><td></br></td></tr>');

      // ALLIEVI DISPERSI
      $allievidispersi = $num_alunni - $alunniassenti - $alunnievacuati;
      echo('<tr><td><input type="hidden" name="alunnidispersi" value="' . $allievidispersi . '"</tr></td>');
      if(!$idclasse || $allievidispersi < 0)
      {
        $allievidispersi = 0;
      }
      print('
              <tr>
              <td width="50%"><b>Allievi dispersi: </b></td>
              <td width="50%">');
      if($allievidispersi != 0)
      {
        print('<font color="red">');
      }
      print($allievidispersi);
      if($allievidispersi != 0)
      {
        print('</font>');
      }
      print('</td></tr>');

      print('<tr><td></br></td></tr>');

      //INDICARE ALLIEVI DISPERSI
      for($n=0; $n<$allievidispersi; $n++)
      {
        ${'disperso'.($n+1)} = stringa_html('disperso'.($n+1));
      }
      for($n = 0; $n < $allievidispersi; $n++)
      {
        mysqli_data_seek($ris, 0);
        print('
                <tr>
                <td width="50%"><b><font color="red">Disperso ');
                print($n+1);
                print(': </font></b></td>
                <td width="50%">
                <SELECT ID="disperso');
                print($n+1);
                print('" NAME="disperso');
                print($n+1);
                print('" ONCHANGE="evacuazione.submit()"><option value="">&nbsp;');
                while($dati = mysqli_fetch_array($ris))
                {
                    print "<option value='";
                    print $dati['cognome'] . " " . $dati['nome'];
                    print "'";
                    if (${'disperso'.($n+1)} == $dati['cognome'] . " " . $dati['nome'])
                    {
                        print " selected";
                    }
                    print(">");
                    print($dati['cognome'] . " " . $dati['nome']);
                    print "</option>";
                }
                echo('</SELECT></td></tr>');
      }
      if($allievidispersi != 0)
      {
        print('<tr><td></br></td></tr>');
      }

      // APRI FILA E CHIUDI FILA
      mysqli_data_seek($ris, 0);
      print('
              <tr>
              <td width="50%"><b>Alunno apri fila: </b></td>
              <td width="50%">
              <SELECT ID="aprifila" NAME="aprifila" ONCHANGE="evacuazione.submit()"><option value="">&nbsp;');
              while($dati = mysqli_fetch_array($ris))
              {
                  print "<option value='";
                  print $dati['cognome'] . " " . $dati['nome'];
                  print "'";
                  if ($aprifila == $dati['cognome'] . " " . $dati['nome'])
                  {
                      print " selected";
                  }
                  print(">");
                  print($dati['cognome'] . " " . $dati['nome']);
                  print "</option>";
              }
              echo('</SELECT></td></tr>');
      mysqli_data_seek($ris, 0);
      print('
              <tr>
              <td width="50%"><b>Alunno chiudi fila: </b></td>
              <td width="50%">
              <SELECT ID="chiudifila" NAME="chiudifila" ONCHANGE="evacuazione.submit()"><option value="">&nbsp;');
              while($dati = mysqli_fetch_array($ris))
              {
                  print "<option value='";
                  print $dati['cognome'] . " " . $dati['nome'];
                  print "'";
                  if ($chiudifila == $dati['cognome'] . " " . $dati['nome'])
                  {
                      print " selected";
                  }
                  print(">");
                  print($dati['cognome'] . " " . $dati['nome']);
                  print "</option>";
              }
              echo('</SELECT></td></tr>');

        // ZONA DI RACCOLTA E TIPO DI EMERGENZA
        print('
                <tr>
                <td width="50%"><b>Zona di raccolta: </b></td>
                <td width="50%">');
        print('<input type="text" name="zonaraccolta" size="25" ONCHANGE="evacuazione.submit()" value="');
        print("$zonaraccolta");
        print('"></td></tr>');

        print('
                <tr>
                <td width="50%"><b>Tipo di emergenza: </b></td>
                <td width="50%">');
        print('<input type="text" name="tipoemergenza" size="25" ONCHANGE="evacuazione.submit()" value="');
        print("$tipoemergenza");
        print('"></td></tr>');

        print('<tr><td></br></td></tr>');

        // INSEGNANTE
        $sql_doc = "SELECT * FROM tbl_docenti ,tbl_utenti
        WHERE tbl_docenti.iddocente=tbl_utenti.idutente
        ORDER BY cognome,nome";
        $ris_doc = mysqli_query($con, inspref($sql_doc));
        print('
                <tr>
                <td width="50%"><b>Insegnante: </b></td>
                <td width="50%">
                <SELECT ID="insegnante" NAME="insegnante" ONCHANGE="evacuazione.submit()"><option value="">&nbsp;');
                while($dati = mysqli_fetch_array($ris_doc))
                {
                    print "<option value='";
                    print $dati['cognome'] . " " . $dati['nome'];
                    print "'";
                    if ($insegnante == $dati['cognome'] . " " . $dati['nome'])
                    {
                        print " selected";
                    }
                    print(">");
                    print($dati['cognome'] . " " . $dati['nome']);
                    print "</option>";
                }
                echo('</SELECT></td></tr>');

                print('<tr><td></br></br></td></tr>');

                // PULSANTE SUBMIT
                print('<tr><td width="50%"><center>');
                print('<input type="submit" formaction="stampa_evacuazione.php?preview=1" formtarget="_blank" value="Visualizza Report">');
                print('</center></td>');
                print('<td width="50%"><center>');
                print('<input type="submit" formaction="stampa_evacuazione.php?preview=0" formtarget="_blank" value="Salva Report">');
                print('</center></td></tr>');

                print('<tr><td></br></br></td></tr>');
        }
    }
}
echo('</table>');
echo('</form>');

mysqli_close($con);
stampa_piede("");
