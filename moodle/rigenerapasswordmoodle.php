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

// preparazione del link per tornare indietro nel registro di classe

$titolo = "Rigenerazione password Moodle";
$script = "<script>
function checkTutti()
{
   with (document.rigenera)
   {
      for (var i=0; i < elements.length; i++)
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = true;
      }
   }
}
function uncheckTutti()
{
   with (document.rigenera)
   {
      for (var i=0; i < elements.length; i++)
      {
         if (elements[i].type == 'checkbox')
            elements[i].checked = false;
      }
   }
}

</script>
";


$nome = stringa_html('cl');

stampa_head($titolo, "", $script, "MSP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a>$goback[1] - $titolo", "", "$nome_scuola", "$comune_scuola");

print ('
   <form method="post" action="rigenerapasswordmoodle.php" name="rigenerapassmoodle">
         <input type="hidden" name="goback" value="' . $goback[0] . '">
         <input type="hidden" name="idclasse" value="' . $nome . '">
   
   <p align="center">
   <table align="center">
   <tr>
      <td width="50%"><p align="center"><b>Classe</b></p></td>
      <td width="50%">
      <SELECT ID="cl" NAME="cl" ONCHANGE="rigenerapassmoodle.submit()">');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$query = "SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno";
print "<option value=''>&nbsp;";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));

while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";

    if ($nome == $nom["idclasse"])
    {
        print " selected";
    }

    print ">{$nom['anno']}&nbsp;{$nom['sezione']}&nbsp;{$nom['specializzazione']}</option>";
}

echo("
      </SELECT>
      </td></tr>

       </table>
    </form><hr/>
");



if ($nome != "")
{
    $idclasse = $nome;
    $classe = "";


    $query = "SELECT idalunno AS al,cognome, nome, datanascita FROM tbl_alunni WHERE idclasse = $idclasse  ORDER BY cognome, nome, datanascita";
    //print inspref($query);
    $ris = mysqli_query($con, inspref($query)) or die("Errore: " . inspref($query, false));



    print "<br><br><center><input type='button' value='Seleziona tutti' onclick='checkTutti()'>
           <input type='button' value='Deseleziona tutti' onclick='uncheckTutti()'></center><br>";


    print ("<form name='rigenera' action='rigenerapasswordmoodleins.php' method='post'><table border=1 align=center>");
    print "
          <tr class=prima >
          <td ><b > N . </b ></td >
          <td ><b > Alunno </b ></td >
          
          <td ><b > Rigenera  </b ></td >

          </tr >
";


    $cont = 0;
    while ($val = mysqli_fetch_array($ris))
    {
        $cont++;

        print "
               <tr>
                 <td ><b > " . $cont . "</b ></td ><td ><b> " . $val["cognome"] . " " . $val["nome"] . " " . data_italiana($val["datanascita"]) . "</b ></td >
                <td ><center >   <input type = 'checkbox' name = 'rig" . $val["al"] . "'></td></tr>";
    }

    print "</table>";

    print "<p align = center><input type = submit name = b value = Rigenera >";
    print "<p align = center ><input type = hidden value = " . $idclasse . " name = cl >
      </form >";
}
else
{

    if ($nome == "")
    {
        print("");
    }
}
// fine if

mysqli_close($con);
stampa_piede("");
