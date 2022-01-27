<?php

require_once '../lib/req_apertura_sessione.php';

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

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//
//    Parte iniziale della pagina
//

$titolo = "Inserimento e modifica valutazioni obiettivi primo quadrimestre";
$script = "";

stampa_head($titolo, "", $script, "DS");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

//
//    Condizione per classi da selezionare in base al livello scuola
//
/*
  if ($_SESSION['livello_scuola'] == 1)
  $annocomp = "anno = '5'";
  if ($_SESSION['livello_scuola'] == 2)
  $annocomp = "anno = '3'";
  if ($_SESSION['livello_scuola'] == 3)
  $annocomp = "anno = '5' or anno = '8'";
  if ($_SESSION['livello_scuola'] == 4)
  $annocomp = "anno = '5'";
 */
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$idclasse = stringa_html('idclasse');
$idalunno = stringa_html('idalunno');
$idmateria = stringa_html('idmateria');
/* if ($idalunno!="")
  $idclasse= estrai_classe_alunno($idalunno, $con);
  else
  $idclasse=0;
 */
$id_ut_doc = $_SESSION["idutente"];

print ('
         <form method="post" action="obproposteint.php" name="voti">
   
         <p align="center">
         <table align="center">

         ');

//
//   Leggo il nominativo del docente e lo visualizzo
//

$query = "select cognome, nome from tbl_docenti where idutente='$id_ut_doc'";
$ris = eseguiQuery($con, $query);
if ($nom = mysqli_fetch_array($ris))
{
    $cognomedoc = $nom["cognome"];
    $nomedoc = $nom["nome"];
    $nominativo = $nomedoc . " " . $cognomedoc;
}


print("    
        <tr>
              <td><b>Docente</b></td>

          <td>
          <INPUT TYPE='text' VALUE='$nominativo' disabled>
          <input type='hidden' value='$iddocente' name='iddocente'>
          </td></tr>");

//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%">
        <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="voti.submit()"><option value="">');

$query = "select distinct tbl_cattnosupp.idclasse, anno, sezione, specializzazione from tbl_cattnosupp, tbl_classi where tbl_cattnosupp.idclasse=tbl_classi.idclasse and iddocente='$id_ut_doc' order by anno, sezione, specializzazione";

$ris = eseguiQuery($con, $query);
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
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
//  ALUNNI
//

if ($idclasse != '')
{
    if (scrutinio_aperto($idclasse, 1, $con))
    {
        print('
        <tr>
        <td width="50%"><b>Materia</b></td>
        <td width="50%">
        <SELECT ID="idmateria" NAME="idmateria" ONCHANGE="voti.submit()"><option value="">');

        $query = "select distinct tbl_materie.idmateria, denominazione from tbl_cattnosupp, tbl_materie "
                . "where tbl_cattnosupp.idmateria=tbl_materie.idmateria "
                . "and idclasse='$idclasse'"
                . "and iddocente='$id_ut_doc' order by denominazione";
        // print inspref($query);
        $ris = eseguiQuery($con, $query);
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value='";
            print ($nom["idmateria"]);
            print "'";
            if ($idmateria == $nom["idmateria"])
            {
                print " selected";
            }
            print ">";
            print ($nom["denominazione"]);
        }

        echo('
      </SELECT>
      </td></tr>');
    }
    else
        
        print "<tr><td colspan=2><b><center><br>Scrutinio già chiuso!<br></center></b></td></tr>";
        
    if ($idclasse != '' && $idmateria != '')
    {
        print('
        <tr>
        <td width="50%"><b>Alunno</b></td>
        <td width="50%">
        <SELECT ID="idalunno" NAME="idalunno" ONCHANGE="voti.submit()"><option value="">');

        $query = "select idalunno, cognome, nome, datanascita from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";

        $ris = eseguiQuery($con, $query);
        while ($nom = mysqli_fetch_array($ris))
        {
            print "<option value='";
            print ($nom["idalunno"]);
            print "'";
            if ($idalunno == $nom["idalunno"])
            {
                print " selected";
            }
            print ">";
            print ($nom["cognome"]);
            print "&nbsp;";
            print($nom["nome"]);
            print "&nbsp;(";
            print(data_italiana($nom["datanascita"]));
            print ")";
        }

        echo('
      </SELECT>
      </td></tr>');
    } 
}
echo('</table>
 
       ');

echo('</form><hr>');

//
//  Se è stato selezionato l'alunno si procede all'inserimento/modifica delle
//  valutazioni
//
//Stabilisco il livello scuola

/*
$annoclasse = decodifica_anno_classe($idclasse, $con);

if ($annoclasse == 3 || $annoclasse == 8)
    $livscuola = 2;
if ($annoclasse == 5)
    $livscuola = $_SESSION['livello_scuola']; */

if ($idalunno != '')
{
    print "<form name='regprop' action='obinsproposteint.php' method='POST'>";
    print "<input type='hidden' name='iddocente' value='$id_ut_doc'>";
    print "<input type='hidden' name='idalunno' value='$idalunno'>";
    print "<input type='hidden' name='idclasse' value='$idclasse'>";
    print "<input type='hidden' name='idmateria' value='$idmateria'>";

    print "<table border=1 align = 'center'>";
    print "<tr class='prima'><td>Progr.</td><td>Obiettivo</td><td>Valutazione</td></tr>";
    $query = "select * from tbl_obiettivi where idmateria=$idmateria and idclasse=$idclasse order by progressivo";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        
        print "<tr>";
        print "<td valign='middle' width=5%>" . $rec['progressivo'] . "</td>";

        print "<td valign='middle' width=60%>" . $rec['obiettivo'] . "</td>";
        // CERCO EVENTUALE VALUTAZIONE GIA' INSERITA
        $idlivelloob="";
        $query="select * from tbl_valutazioniobiettivi where idalunno=$idalunno and idobiettivo=".$rec['idobiettivo']." and periodo=1";
        
        $risvalob= eseguiQuery($con,$query);
        if (mysqli_num_rows($risvalob)==1)
        {
            
            $recvalob= mysqli_fetch_array($risvalob);
            $idlivelloob=$recvalob['idlivelloobiettivo'];
        }
        
        
         print("<td>
        
        <SELECT ID='idlivelloob_".$rec['idobiettivo']."' NAME='idlivelloob_".$rec['idobiettivo']."' ><option value=''>");

        $query = "select * from tbl_livelliobiettivi ";

        $rislivob = eseguiQuery($con, $query);
        while ($nomlivob = mysqli_fetch_array($rislivob))
        {
            print "<option value='";
            print ($nomlivob["idlivelloobiettivo"]);
            print "'";
            if ($idlivelloob == $nomlivob["idlivelloobiettivo"])
            {
                print " selected";
            }
            print ">";
            print ($nomlivob["abbreviazione"]);
            
        }

        echo('
      </SELECT></td>');
        
        
        
      
    }
    print "</tr>";
}
print "</table>";

print "<center><br><input type='submit' value='Registra proposte'><br>";

print "</form>";

mysqli_close($con);
stampa_piede("");

